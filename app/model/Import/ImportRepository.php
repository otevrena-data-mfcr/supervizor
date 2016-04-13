<?php

/*
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace App\Model\Repository;

use App\Model\Entities\Import;
use App\Model\Entities\ImportGroup;
use Kdyby\Doctrine\EntityManager;

class ImportRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $importGroupRepository;

    /** @var \Kdyby\Doctrine\EntityRepository */
    private $importRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * ImportRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->importGroupRepository = $entityManager->getRepository(ImportGroup::class);
        $this->importRepository = $entityManager->getRepository(Import::class);
    }


    public function setImportGroup($name, $slug, $isDefault)
    {
        /** @var ImportGroup $foundImportGroup */
        $foundImportGroup = $this->importGroupRepository->findOneBy(['slug' => $slug]);
        if ($foundImportGroup) {
            $foundImportGroup->setName($name);
            $foundImportGroup->setIsDefault($isDefault);
        } else {
            $foundImportGroup = new ImportGroup($name, $slug, $isDefault);
        }


        $this->entityManager->persist($foundImportGroup);

        $this->entityManager->flush();

        return $foundImportGroup;
    }

    public function setImport($importGroup, $name, $slug, $description, $homepage, $isDefault)
    {
        /** @var Import $foundImport */
        $foundImport = $this->importRepository->findOneBy(['slug' => $slug, 'importGroup' => $importGroup]);
        if ($foundImport) {
            $foundImport->setImportGroup($importGroup);
            $foundImport->setName($name);
            $foundImport->setIsDefault($isDefault);
            $foundImport->setDescription($description);
            $foundImport->setHomepage($homepage);
        } else {
            $foundImport = new Import($importGroup, $name, $slug, $description, $homepage, $isDefault);
        }


        $this->entityManager->persist($foundImport);

        $this->entityManager->flush();

        return $foundImport;
    }
}
