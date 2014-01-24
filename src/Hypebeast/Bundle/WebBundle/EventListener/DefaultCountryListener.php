<?php

namespace Hypebeast\Bundle\WebBundle\EventListener;

use Maxmind\Bundle\GeoipBundle\Service\GeoipManager;
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Bundle\AddressingBundle\Model\Country;
use Sylius\Bundle\AddressingBundle\Model\Zone;

class DefaultCountryListener
{
    protected $repository;
    protected $ipLocator;
    protected $defaultIsoName;
    protected $id;
    protected $parameterName;

    public function __construct(EntityRepository $zoneRepository, EntityRepository $memberRepository, EntityRepository $countryRepository, GeoipManager $ipLocator, $defaultIsoName, $ip = null)
    {
        $this->zoneRepository    = $zoneRepository;
        $this->memberRepository  = $memberRepository;
        $this->countryRepository = $countryRepository;
        $this->ipLocator         = $ipLocator;
        $this->defaultIsoName    = $defaultIsoName;
        $this->ip                = $ip;
        $this->parameterName     = '_hypebeast_default_';
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        if (null !== $id = $request->request->get($this->parameterName . 'country', null)) {
            $session->set($this->parameterName . 'country', $this->countryRepository->find($id));
            $session->remove($this->parameterName . 'zone');
        }

        if (false === $session->has($this->parameterName . 'country') || !$session->get($this->parameterName . 'country') ) {
            $locator = $this->buildLocator($request);
            $country = $this->findCountry(false === $locator ? null : $locator);

            $session->set($this->parameterName . 'country', $country);
        } else {
            $session->set(
                $this->parameterName . 'country',
                $this->refreshCountry($session->get($this->parameterName . 'country'))
            );
        }

        if (false === $session->has($this->parameterName . 'zone')) {
            $zone = $this->findZone(
                $session->get($this->parameterName . 'country')
            );

            if (null !== $zone) {
                $session->set($this->parameterName . 'zone', $zone);
            }
        } else {
            $session->set(
                $this->parameterName . 'zone',
                $this->refreshZone($session->get($this->parameterName . 'zone'))
            );
        }
    }

    protected function buildLocator(Request $request)
    {
        return $this
            ->ipLocator
            ->lookup(
                null === $this->ip
                    ? $request->getClientIp()
                    : $this->ip
        );
    }

    protected function findCountry(GeoipManager $locator = null)
    {
        if (null === $locator) {

            return $this->getDefaultCountry();
        }

        if (null === $country = $this->countryRepository->findOneBy([ 'isoName' => $locator->getCountryCode() ])) {

            return $this->getDefaultCountry();
        }

        return $country;
    }

    protected function getDefaultCountry()
    {
        if (null !== $country = $this->countryRepository->findOneBy([ 'isoName' => $this->defaultIsoName ])) {

            return $country;
        }

        throw new \Exception(sprintf("Default country \"%s\" can't be found", $this->defaultIsoName));
    }

    protected function findZone(Country $country)
    {
        $zones = array_map(
            function ($e) {
                return $e->getBelongsTo();
            },
            $this->getZonesFromCountry($country)
        );

        return empty($zones) ? null : current($zones);
    }

    protected function refreshCountry(Country $country)
    {
        return $this->countryRepository->find($country->getId());
    }

    protected function refreshZone(Zone $zone)
    {
        return $this->zoneRepository->find($zone->getId());
    }

    protected function getZonesFromCountry(Country $country)
    {
        $qb = $this->memberRepository->createQueryBuilder('cm');
        $qb->join('cm.belongsTo', 'z', 'INNER');

        $qb
            ->andWhere('cm.country = :country')
            ->setParameter('country', $country)
        ;

        $qb->select('cm, z');

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
