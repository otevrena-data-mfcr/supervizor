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

namespace App\Model\Fixtures\DefaultData;

use App\Model\Entities\UserGroup;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserGroupFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * UsersFixtures constructor.
     */
    public function __construct()
    {
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {        
        $userGroups = [];
        $userGroups[] = [
            'name' => 'Admin',
            'description' => 'Technický administrátor systému. Nemá přístup k běžným funkcím aplikace ani žádným datům. Spravuje uživatele a jejich role. Řeší chyby a výjimky.',
            'colorClass' => 'danger',
            'register' => false
        ];
        
        $userGroups[] = [
            'name' => 'Manažer',
            'description' => '"Manažer" má nad systémem ze všech rolí největší moc. Může dělat věci, ke kterým nikdo jiný nemá přístup (např. určování cen), zároveň ale nemá přístup k různým technickým detailům nebo účetním specialitám.',
            'colorClass' => 'info',
            'register' => false
        ];
        
        $userGroups[] = [
            'name' => 'Subjekt',
            'description' => 'Tuto roli mají všichni registrovaní zákazníci. Poskytuje oprávnění pro nakládání se zákaznickým účtem, nastavování stávajících a objednávání nových služeb.',
            'colorClass' => 'default',
            'register' => true
        ];
        
        $userGroups[] = [
            'name' => 'Technik',
            'description' => 'Technik se stará o technické záležitosti - nastavení služeb jako mail, FTP. Má přístup k informacím o službách, subjektech, výzvách apod.',
            'colorClass' => 'success',
            'register' => false
        ];
        
        $userGroups[] = [
            'name' => 'Účetní',
            'description' => 'Role "účetní" zpřístupňuje speciální účetní funkce systému a dále poskytuje uživateli read-only přístup k některým částem databáze.',
            'colorClass' => 'warning',
            'register' => false
        ];

        foreach ($userGroups AS $userGroupSrc)
        {
            $userGroup = new UserGroup($userGroupSrc['name'], $userGroupSrc['description'], $userGroupSrc['colorClass'], $userGroupSrc['register']);
                        
            $manager->persist($userGroup);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }

}
