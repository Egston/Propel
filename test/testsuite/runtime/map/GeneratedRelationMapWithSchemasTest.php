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
 * Test class for PHP5TableMapBuilder with schemas.
 *
 * @author     Ulf Hermann
 * @version    $Id$
 * @package    runtime.map
 */
class GeneratedRelationMapWithSchemasTest extends SchemasTestBase
{
    protected $databaseMap;

    protected function setUp()
    {
        parent::setUp();
        $this->databaseMap = Propel::getDatabaseMap('bookstore-schemas');
    }

    public function testGetRightTable()
    {
        $bookTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaBookstoreBookstore');
        $contestTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaContestBookstoreContest');
        $this->assertEquals(
            $bookTable->getName(),
            $contestTable->getRelation('Propel1TestSchemaBookstoreBookstore')->getRightTable()->getName(),
            'getRightTable() returns correct table when called on a many to one relationship'
        );
        $this->assertEquals(
            $contestTable->getName(),
            $bookTable->getRelation('Propel1TestSchemaContestBookstoreContest')->getRightTable()->getName(),
            'getRightTable() returns correct table when called on a one to many relationship'
        );
        $bookCustomerTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaBookstoreCustomer');
        $bookCustomerAccTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaBookstoreCustomerAccount');
        $this->assertEquals(
            $bookCustomerAccTable->getName(),
            $bookCustomerTable->getRelation('Propel1TestSchemaBookstoreCustomerAccount')->getRightTable()->getName(),
            'getRightTable() returns correct table when called on a one to one relationship'
        );
        $this->assertEquals(
            $bookCustomerTable->getName(),
            $bookCustomerAccTable->getRelation('Propel1TestSchemaBookstoreCustomer')->getRightTable()->getName(),
            'getRightTable() returns correct table when called on a one to one relationship'
        );
    }

    public function testColumnMappings()
    {
        $contestTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaContestBookstoreContest');
        $this->assertEquals(array('propel1_test_schema_contest.bookstore_contest.bookstore_id' => 'propel1_test_schema_bookstore.bookstore.id'), $contestTable->getRelation('Propel1TestSchemaBookstoreBookstore')->getColumnMappings(), 'getColumnMappings returns local to foreign by default');
        $this->assertEquals(array('propel1_test_schema_contest.bookstore_contest.bookstore_id' => 'propel1_test_schema_bookstore.bookstore.id'), $contestTable->getRelation('Propel1TestSchemaBookstoreBookstore')->getColumnMappings(RelationMap::LEFT_TO_RIGHT), 'getColumnMappings returns local to foreign when asked left to right for a many to one relationship');

        $bookTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaBookstoreBookstore');
        $this->assertEquals(array('propel1_test_schema_contest.bookstore_contest.bookstore_id' => 'propel1_test_schema_bookstore.bookstore.id'), $bookTable->getRelation('Propel1TestSchemaContestBookstoreContest')->getColumnMappings(), 'getColumnMappings returns local to foreign by default');
        $this->assertEquals(array('propel1_test_schema_bookstore.bookstore.id' => 'propel1_test_schema_contest.bookstore_contest.bookstore_id'), $bookTable->getRelation('Propel1TestSchemaContestBookstoreContest')->getColumnMappings(RelationMap::LEFT_TO_RIGHT), 'getColumnMappings returns foreign to local when asked left to right for a one to many relationship');

        $bookCustomerTable = $this->databaseMap->getTableByPhpName('Propel1TestSchemaBookstoreCustomer');
        $this->assertEquals(array('propel1_test_schema_bookstore.customer_account.customer_id' => 'propel1_test_schema_bookstore.customer.id'), $bookCustomerTable->getRelation('Propel1TestSchemaBookstoreCustomerAccount')->getColumnMappings(), 'getColumnMappings returns local to foreign by default');
        $this->assertEquals(array('propel1_test_schema_bookstore.customer.id' => 'propel1_test_schema_bookstore.customer_account.customer_id'), $bookCustomerTable->getRelation('Propel1TestSchemaBookstoreCustomerAccount')->getColumnMappings(RelationMap::LEFT_TO_RIGHT), 'getColumnMappings returns foreign to local when asked left to right for a one to one relationship');
    }

}
