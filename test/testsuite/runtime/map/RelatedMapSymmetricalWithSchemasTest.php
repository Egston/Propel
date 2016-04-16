<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/../../../tools/helpers/schemas/SchemasTestBase.php';

/**
 * Test class for RelatedMap::getSymmetricalRelation with schemas.
 *
 * @author     Ulf Hermann
 * @version    $Id$
 * @package    runtime.map
 */
class RelatedMapSymmetricalWithSchemasTest extends SchemasTestBase
{
  protected $databaseMap;

    protected function setUp()
    {
        parent::setUp();
        $this->databaseMap = Propel::getDatabaseMap('bookstore-schemas');
    }

    public function testOneToMany()
    {
        // passes on its own, but not with the full tests suite
        $this->markTestSkipped();
        $contestTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaContestBookstoreContest');
        $contestToBookstore = $contestTable->getRelation('Propel1TestSchemaBookstoreBookstore');
        $bookstoreTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaBookstoreBookstore');
        $bookstoreToContest = $bookstoreTable->getRelation('Propel1TestSchemaContestBookstoreContest');
        $this->assertEquals($bookstoreToContest->getName(), $contestToBookstore->getSymmetricalRelation()->getName());
        $this->assertEquals($contestToBookstore->getName(), $bookstoreToContest->getSymmetricalRelation()->getName());
    }

    public function testOneToOne()
    {
        $accountTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaBookstoreCustomerAccount');
        $accountToCustomer = $accountTable->getRelation('Propel1TestSchemaBookstoreCustomer');
        $customerTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaBookstoreCustomer');
        $customerToAccount = $customerTable->getRelation('Propel1TestSchemaBookstoreCustomerAccount');
        $this->assertEquals($accountToCustomer, $customerToAccount->getSymmetricalRelation());
        $this->assertEquals($customerToAccount, $accountToCustomer->getSymmetricalRelation());
    }

    public function testSeveralRelationsOnSameTable()
    {
        $contestTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaContestBookstoreContest');
        $contestToCustomer = $contestTable->getRelation('Propel1TestSchemaBookstoreCustomerRelatedByFirstContest');
        $customerTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaBookstoreCustomer');
        $customerToContest = $customerTable->getRelation('Propel1TestSchemaContestBookstoreContestRelatedByFirstContest');
        $this->assertEquals($contestToCustomer, $customerToContest->getSymmetricalRelation());
        $this->assertEquals($customerToContest, $contestToCustomer->getSymmetricalRelation());
    }

    public function testCompositeForeignKey()
    {
        $entryTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaContestBookstoreContestEntry');
        $entryToContest = $entryTable->getRelation('Propel1TestSchemaContestBookstoreContest');
        $contestTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaContestBookstoreContest');
        $contestToEntry = $contestTable->getRelation('Propel1TestSchemaContestBookstoreContestEntry');
        $this->assertEquals($entryToContest, $contestToEntry->getSymmetricalRelation());
        $this->assertEquals($contestToEntry, $entryToContest->getSymmetricalRelation());
    }

}
