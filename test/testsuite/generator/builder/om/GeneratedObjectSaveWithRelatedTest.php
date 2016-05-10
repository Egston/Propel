<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/../../../../tools/helpers/bookstore/BookstoreEmptyTestBase.php';
require_once dirname(__FILE__) . '/../../../../tools/helpers/bookstore/BookstoreCountableClasses.php';
require_once dirname(__FILE__) . '/../../../../tools/helpers/bookstore/EventsAwareBook.php';
require_once dirname(__FILE__) . '/../../../../tools/helpers/bookstore/EventsAwareBookSummary.php';

/**
 * Tests relationships between generated Object classes.
 *
 * This test uses generated Bookstore classes to test the behavior of various
 * object operations.  The _idea_ here is to test every possible generated method
 * from Object.tpl; if necessary, bookstore will be expanded to accommodate this.
 *
 * The database is reloaded before every test and flushed after every test.  This
 * means that you can always rely on the contents of the databases being the same
 * for each test method in this class.  See the BookstoreDataPopulator::populate()
 * method for the exact contents of the database.
 *
 * @see        BookstoreDataPopulator
 * @author     Hans Lellelid <hans@xmpl.org>
 * @package    generator.builder.om
 */
class GeneratedObjectSaveWithRelatedTest extends BookstoreEmptyTestBase
{

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @covers PHP5ObjectBuilder::addIsDirtyWithRelated
     * @covers PHP5ObjectBuilder::addSaveWithRelated
     */
    public function testSaveWithRelated()
    {
        Count::reset();

        // create 5 books, each with 5 summaries, attached to single author
        $books = new PropelObjectCollection();

        for ($i = 0; $i < 5; $i++) {
            $book = new CountableBook();
            $book->setISBN($i);
            $books[] = $book;
            for ($j = 0; $j < 5; $j++) {
                $summary = new CountableBookSummary();
                $summary->setSummary("book $i / summary $j");
                $summary->setSummarizedBook($book);
            }
        }
        /* @var $firstBook CountableBook */
        $firstBook = $books->getFirst();
        /* @var $lastBook CountableBook */
        $lastBook = $books->getLast();
        $author = new CountableAuthor();
        $author->setLastName('John Dee');
        $author->setBooks($books);

        $this->assertEquals(0, Count::get(null, 'isDirtyWithRelated_Initial'), 'No isDirtyWithRelated() should be invoked yet');
        $this->assertEquals(0, Count::get(null, 'isDirtyWithRelated_Recursive'), 'No isDirtyWithRelated() should be invoked yet');

        $this->assertTrue(($author->isDirtyWithRelated()));
        $this->assertEquals(1, Count::get('CountableAuthor', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(5, Count::get('CountableBook', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(25, Count::get('CountableBookSummary', 'isDirtyWithRelated_Recursive'));
        Count::reset('CountableAuthor');

        $this->assertTrue(($firstBook->isDirty()));
        $this->assertTrue(($firstBook->isDirtyWithRelated()));
        $this->assertTrue(($lastBook->isDirtyWithRelated()));

        // Check count of isDirtyWithRelated() invoked during save()
        Count::reset();
//        $this->describeAuthorRelated($author);
        $firstBook->getBookSummarys()->getLast()->saveWithRelated();
//        $this->describeAuthorRelated($author);
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(1, Count::get('CountableAuthor', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(5, Count::get('CountableBook', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(2, Count::get('CountableBookSummary', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(24, Count::get('CountableBookSummary', 'isDirtyWithRelated_Recursive'));

        // Check count of isDirtyWithRelated() calls when no object in chain is modified
        Count::reset();
        $this->assertFalse(($author->isDirtyWithRelated()));
        $this->assertEquals(1, Count::get('CountableAuthor', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirty'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirty'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirty'));

        Count::reset();
        $this->assertFalse(($firstBook->isDirtyWithRelated()));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(1, Count::get('CountableBook', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Recursive'));

        Count::reset();
        $this->assertFalse(($lastBook->isDirtyWithRelated()));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(1, Count::get('CountableBook', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Recursive'));


        // Add new summary
//        $this->describeAuthorRelated($author);
        $additionalSummary = new CountableBookSummary();
        $this->assertEquals(31, count($firstBook->_related));
        $this->assertSame($firstBook->_related, $lastBook->_related);
        $this->assertFalse($firstBook->isDirtyWithRelated);
        $this->assertFalse($lastBook->isDirtyWithRelated);

        $lastBook->addBookSummary($additionalSummary);
        $additionalSummary->setSummary("book 4 / summary 5");

        $this->assertEquals(0, count($firstBook->_related));
        $this->assertNull($firstBook->isDirtyWithRelated);
        $this->assertEquals(0, count($lastBook->_related));
        $this->assertNull($lastBook->isDirtyWithRelated);
//        $this->describeAuthorRelated($author);

        Count::reset();
        $this->assertTrue(($author->isDirtyWithRelated()));
//        $this->describeAuthorRelated($author);
        $this->assertEquals(1, Count::get('CountableAuthor', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(1, Count::get('CountableAuthor', 'isDirty'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(5, Count::get('CountableBook', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(5, Count::get('CountableBook', 'isDirty'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(26, Count::get('CountableBookSummary', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(26, Count::get('CountableBookSummary', 'isDirty'));

        Count::reset();
        $printTraces = false; // debug option to print traces

        if ($printTraces) {
            Count::collectTraces(true);
            $this->assertTrue(($firstBook->isDirtyWithRelated()));
            Count::collectTraces(false);
            $this->setupDescFuncs();
            Count::printTraces('CountableBook', 'isDirtyWithRelated_Recursive');
            Count::printTraces('CountableBook', 'isDirtyWithRelated_Initial');
        } else {
            $this->assertTrue(($firstBook->isDirtyWithRelated()));
        }
//        $this->describeAuthorRelated($author);
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(1, Count::get('CountableBook', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirty'));

        $author->setAge(42);
        Count::reset();
        $this->assertTrue(($author->isDirtyWithRelated()));
        $this->assertEquals(1, Count::get('CountableAuthor', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(1, Count::get('CountableAuthor', 'isDirty'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(5, Count::get('CountableBook', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(26, Count::get('CountableBookSummary', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(26, Count::get('CountableBookSummary', 'isDirty'));

        // Check count of isDirtyWithRelated() invoked during saving of the new summary
        Count::reset();
        $additionalSummary->saveWithRelated();
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(1, Count::get('CountableAuthor', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(5, Count::get('CountableBook', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(2, Count::get('CountableBookSummary', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(25, Count::get('CountableBookSummary', 'isDirtyWithRelated_Recursive'));

        $this->assertFalse(($author->isModified()));
        $this->assertFalse(($additionalSummary->isModified()));
        $this->assertFalse(($additionalSummary->isNew()));
        $this->assertFalse(($author->isDirtyWithRelated()));

        // Detach book from author
        $author->removeBook($lastBook);
        Count::reset();
        $this->assertTrue($author->isDirtyWithRelated(), 'Book is scheduled for deletion');
        $this->assertEquals(1, Count::get('CountableAuthor', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(4, Count::get('CountableBook', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(20, Count::get('CountableBookSummary', 'isDirtyWithRelated_Recursive'));

        Count::reset();
        $this->assertTrue($lastBook->isDirtyWithRelated());
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(1, Count::get('CountableBook', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(6, Count::get('CountableBookSummary', 'isDirtyWithRelated_Recursive'));

        $additionalSummary->setSummary('test1');
        $this->assertTrue($lastBook->isDirtyWithRelated());
        $this->assertTrue($author->isDirtyWithRelated());
        $author->saveWithRelated(); // should also save objects related to the book scheduled for deletion
        $this->assertFalse($lastBook->isDirtyWithRelated());
        $this->assertFalse($author->isDirtyWithRelated());

        // Delete the detached book
        $lastBook->delete();
        $this->assertTrue(($lastBook->isDeleted()));

        // Save summary of the deleted book, to check won't attempt to save the
        // deleted book
        $additionalSummary->setSummary('test2');

        try {
            $additionalSummary->saveWithRelated(); //
        } catch (PropelException $e) {
            $this->fail('Must not attempt to save related book which was deleted');
        }

        // Test that isDirtyWithRelated prop is false after isDirtyWithRelated() returns
        $this->assertFalse($additionalSummary->isDirtyWithRelated());
        $this->assertFalse($additionalSummary->alreadyInSaveWithRelated);
        $additionalSummary->saveWithRelated();
        $this->assertFalse($additionalSummary->alreadyInSaveWithRelated);

        Count::reset();
    }

    /**
     * Tests circular dependency
     *
     * @covers PHP5ObjectBuilder::addIsDirtyWithRelated
     * @covers PHP5ObjectBuilder::addSaveWithRelated
     */
    public function testSaveWithRelatedCircularDependency()
    {
        $book = new EventsAwareBook();

        // test circular dependency

        $summary = new EventsAwareBookSummary();
        $summary->setSummary('official summary');
        $e = new BookOfficialSummaryEvent();
        $e->setNewOfficialSummary($summary);
        $book->addBookOfficialSummaryEvent($e);

        $this->assertTrue($e->isDirtyWithRelated());
        $this->assertCount(3, $e->_related);

        $e->saveWithRelated();

        $this->assertSame($book->getOfficialSummary(), $summary);

        $this->assertFalse($book->isModified());
        $this->assertFalse($e->isModified());
        $this->assertFalse($summary->isModified());
        $this->assertFalse($book->isDirtyWithRelated());
    }

    /**
     * Tests circular dependency
     *
     * @covers PHP5ObjectBuilder::addIsDirtyWithRelated
     * @covers PHP5ObjectBuilder::addSaveWithRelated
     */
    public function testSaveWithRelatedCircular2()
    {
        $book = new EventsAwareBook();

        $summary1 = new EventsAwareBookSummary();
        $summary1->setSummary('official summary');
        $e1 = new BookOfficialSummaryEvent();
        $e1->setNewOfficialSummary($summary1);
        $book->addBookOfficialSummaryEvent($e1);
        $this->assertSame($book->getOfficialSummary(), $summary1);

        $this->assertTrue($e1->isDirtyWithRelated());
        $this->assertCount(3, $e1->_related);

        $summary2 = new EventsAwareBookSummary();
        $summary2->setSummary('new official summary');
        $e2 = new BookOfficialSummaryEvent();
        $e2->setNewOfficialSummary($summary2);
        $book->addBookOfficialSummaryEvent($e2);
        $this->assertSame($book->getOfficialSummary(), $summary2);

        $this->assertTrue($e2->isDirtyWithRelated());
        $this->assertCount(5, $e2->_related);

        $e2->saveWithRelated();

        $this->assertSame($book->getOfficialSummary(), $summary2);

        $this->assertFalse($book->isModified());
        $this->assertFalse($e1->isModified());
        $this->assertFalse($summary1->isModified());
        $this->assertFalse($e2->isModified());
        $this->assertFalse($summary2->isModified());
        $this->assertFalse($book->isDirtyWithRelated());
    }

    /**
     * Tests circular dependency
     *
     * @covers PHP5ObjectBuilder::addIsDirtyWithRelated
     * @covers PHP5ObjectBuilder::addSaveWithRelated
     */
    public function testSaveWithRelatedCircular3()
    {
        $book = new EventsAwareBook();

        $summary1 = new EventsAwareBookSummary();
        $summary1->setSummary('official summary');
        $e1 = new BookOfficialSummaryEvent();
        $e1->setNewOfficialSummary($summary1);
        $book->addBookOfficialSummaryEvent($e1);
        $this->assertSame($book->getOfficialSummary(), $summary1);

        $this->assertTrue($e1->isDirtyWithRelated());
        $this->assertCount(3, $e1->_related);

        $summary2 = new EventsAwareBookSummary();
        $summary2->setSummary('new official summary');
        $e2 = new BookOfficialSummaryEvent();
        $e2->setNewOfficialSummary($summary2);
        $book->addBookOfficialSummaryEvent($e2);
        $this->assertSame($book->getOfficialSummary(), $summary2);

        $this->assertTrue($e2->isDirtyWithRelated());
        $this->assertCount(5, $e2->_related);

        $e1->saveWithRelated();

        $this->assertSame($book->getOfficialSummary(), $summary2);

        $this->assertFalse($book->isModified());
        $this->assertFalse($e1->isModified());
        $this->assertFalse($summary1->isModified());
        $this->assertFalse($e2->isModified());
        $this->assertFalse($summary2->isModified());
        $this->assertFalse($book->isDirtyWithRelated());
    }

    /**
     * Tests circular dependency
     *
     * @covers PHP5ObjectBuilder::addIsDirtyWithRelated
     * @covers PHP5ObjectBuilder::addSaveWithRelated
     */
    public function testSaveWithRelatedCircular4()
    {
        $book = new EventsAwareBook();

        $summary1 = new EventsAwareBookSummary();
        $summary1->setSummary('official summary');
        $e1 = new BookOfficialSummaryEvent();
        $e1->setEventsAwareBook($book);
        $e1->setNewOfficialSummary($summary1);
        $this->assertSame($book->getOfficialSummary(), $summary1);

        $this->assertTrue($e1->isDirtyWithRelated());
        $this->assertCount(3, $e1->_related);

        $summary2 = new EventsAwareBookSummary();
        $summary2->setSummary('new official summary');
        $e2 = new BookOfficialSummaryEvent();
        $e2->setEventsAwareBook($book);
        $e2->setNewOfficialSummary($summary2);
        $this->assertSame($book->getOfficialSummary(), $summary2);

        $this->assertTrue($e2->isDirtyWithRelated());
        $this->assertCount(5, $e2->_related);

        $e1->saveWithRelated();

        $this->assertSame($book->getOfficialSummary(), $summary2);

        $this->assertFalse($book->isModified());
        $this->assertFalse($e1->isModified());
        $this->assertFalse($summary1->isModified());
        $this->assertFalse($e2->isModified());
        $this->assertFalse($summary2->isModified());
        $this->assertFalse($book->isDirtyWithRelated());

        $book->reload(true);
        $this->assertEquals('new official summary', $book->getOfficialSummary()->getSummary());
        $this->assertEquals(2, $book->countBookSummarys());
    }

    /**
     * @covers PHP5ObjectBuilder::addIsDirtyWithRelated
     * @covers PHP5ObjectBuilder::addSaveWithRelated
     */
    public function testSaveWithRelatedManyToMany()
    {
        // create 5 books, each with 5 summaries, attached to single author
        $books = new PropelObjectCollection();

        for ($i = 0; $i < 5; $i++) {
            $book = new CountableBook();
            $book->setISBN($i);
            $books[] = $book;
            for ($j = 0; $j < 5; $j++) {
                $summary = new CountableBookSummary();
                $summary->setSummarizedBook($book);
                $summary->setSummary("book $i / summary $j");
            }
        }
        /* @var $firstBook CountableBook */
        $firstBook = $books->getFirst();
        /* @var $lastBook CountableBook */
        $lastBook = $books->getLast();
        $author = new CountableAuthor();
        $author->setBooks($books);

        // create two club lists, first with 1 book, second with 2 books
        $brel1 = new BookListRel();
        $brel1->setBook($firstBook);

        $club1 = new CountableBookClubList();
        $club1->setTheme('one');
        $club1->addBookListRel($brel1);

        $brel2_1 = new BookListRel();
        $brel2_1->setBook($firstBook);
        $brel2_2 = new BookListRel();
        $brel2_2->setBook($lastBook);

        $club2 = new CountableBookClubList();
        $club2->setTheme('two');
        $club2->addBookListRel($brel2_1);
        $club2->addBookListRel($brel2_2);

        Count::reset();

        $printTraces = false; // debug option to print traces

        if ($printTraces) {
            Count::collectTraces(true);
            $author->saveWithRelated();
            Count::collectTraces(false);
        } else {
            $author->saveWithRelated();
        }

        $this->assertFalse($club1->isDirty());
        $this->assertFalse($club2->isDirty());

        // check number of calls issued from save()
        $this->assertEquals(2, Count::get('CountableAuthor', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(0, Count::get('CountableAuthor', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBookClubList', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(4, Count::get('CountableBookClubList', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBook', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(10, Count::get('CountableBook', 'isDirtyWithRelated_Recursive'));
        $this->assertEquals(0, Count::get('CountableBookSummary', 'isDirtyWithRelated_Initial'));
        $this->assertEquals(50, Count::get('CountableBookSummary', 'isDirtyWithRelated_Recursive'));

        if ($printTraces) {
            $this->setupDescFuncs();
            Count::printTraces('CountableAuthor', 'isDirtyWithRelated_Recursive');
            Count::printTraces('CountableBook', 'isDirtyWithRelated_Recursive');
        }

        Count::reset();
    }

    protected function setupDescFuncs()
    {
        Count::setObjDescFunc('CountableAuthor', create_function('CountableAuthor $a', 'return $a->getLastName();'));
        Count::setObjDescFunc('BaseAuthor', create_function('BaseAuthor $a', 'return $a->getLastName();'));
        Count::setObjDescFunc('CountableBook', create_function('CountableBook $b', 'return $b->getISBN();'));
        Count::setObjDescFunc('BaseBook', create_function('CountableBook $b', 'return $b->getISBN();'));
        Count::setObjDescFunc('CountableBookSummary', create_function('CountableBookSummary $s', 'return $s->getSummary();'));
        Count::setObjDescFunc('BaseBookSummary', create_function('CountableBookSummary $s', 'return $s->getSummary();'));
        Count::setObjDescFunc('CountableBookClubList', create_function('CountableBookClubList $c', 'return $c->getTheme();'));
        Count::setObjDescFunc('BaseBookClubList', create_function('CountableBookClubList $c', 'return $c->getTheme();'));
    }

    protected function describeAuthorRelated(CountableAuthor $author)
    {
        $descFuncs = array(
            'CountableAuthor' => create_function('CountableAuthor $a', 'return $a->getLastName();'),
            'BaseAuthor' => create_function('BaseAuthor $a', 'return $a->getLastName();'),
            'CountableBook' => create_function('CountableBook $b', 'return $b->getISBN();'),
            'BaseBook' => create_function('CountableBook $b', 'return $b->getISBN();'),
            'CountableBookSummary' => create_function('CountableBookSummary $s', 'return $s->getSummary();'),
            'BaseBookSummary' => create_function('CountableBookSummary $s', 'return $s->getSummary();'),
        );
        $describe = create_function('BaseObject $o, $descFuncs', 'return get_class($o) ." <".var_export($o->isDirtyWithRelated,true)."> ". $descFuncs[get_class($o)]($o);');
        echo "\n";
        echo $describe($author, $descFuncs) . " affects:\n";
        foreach ($author->_related as $dep_o) {
            echo "\t" . $describe($dep_o, $descFuncs) . "\n";
        }
        foreach ($author->getBooks() as $b) {
            echo $describe($b, $descFuncs) . " affects:\n";
            foreach ($b->_related as $dep_o) {
                echo "\t" . $describe($dep_o, $descFuncs) . "\n";
            }
            /* @var $b CountableBook */
            foreach ($b->getBookSummarys() as $summary) {
                echo $describe($summary, $descFuncs) . " affects:\n";
                foreach ($summary->_related as $dep_o) {
                    echo "\t" . $describe($dep_o, $descFuncs) . "\n";
                }
            }
        }
        echo "\n";
    }
}
