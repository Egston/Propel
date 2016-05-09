<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once 'BookstoreTestBase.php';

class Count {
    public static $counts = array();
    public static $traces = array();
    public static $objDescFuncs = array();

    protected static $collectTraces = false;

    /**
     * Enable or disable collecting of backtraces (initially disabled).
     * @param bool $v
     */
    public static function collectTraces($v) {
        self::$collectTraces = $v;
    }

    public static function inc($class, $what) {
        if (!isset(self::$counts[$class][$what])) {
            self::$counts[$class][$what] = 0;
            self::$traces[$class][$what] = array();
        }

        self::$counts[$class][$what]++;

        if (self::$collectTraces) {
            if (version_compare(PHP_VERSION, '5.3.6') >= 0) {
                self::$traces[$class][$what][] = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
            } elseif (version_compare(PHP_VERSION, '5.2.5') >= 0) {
                self::$traces[$class][$what][] = debug_backtrace(true);
            } else {
                self::$traces[$class][$what][] = debug_backtrace();
            }
        }
    }

    /**
     * Set function to get description of object in backtraces.
     *
     * @param string $class
     * @param callback $callback Function which should return description of
     *                           passed object.
     */
    public static function setObjDescFunc($class, $callback)
    {
        self::$objDescFuncs[$class] = $callback;
    }

    /**
     * Get count.
     *
     * @param string[]|string|null $class
     * @param string[]|string      $what
     *
     * @return int
     */
    public static function get($class, $what) {
        $classList = (array) $class;
        $whatList = (array) $what;

        if (!$classList) {
            $classList = array_keys(self::$counts);
        }

        $count = 0;
        foreach ($classList as $c) {
            foreach ($whatList as $w) {
                if (isset(self::$counts[$c][$w])) {
                    $count += self::$counts[$c][$w];
                }
            }
        }

        return $count;
    }

    /**
     * Get backtraces.
     *
     * @param string $class
     * @param string $what
     *
     * @return int
     */
    public static function getTraces($class, $what) {
        $my_trace = debug_backtrace();
        $callerClass = null;
        foreach ($my_trace as $trace) {
            if (!isset($trace['class'])) {
                break;
            }
            if ($trace['class'] === __CLASS__) {
                continue;
            }
            $callerClass = $trace['class'];
            break;
        }
        $result = array();
        foreach (self::$traces[$class][$what] as $traces) {
            array_shift($traces); // first trace is Count::inc()
            $trace = array();
            foreach ($traces as $t) {
                if (isset($t['object']) && isset($t['class'])) {
                    $c = $t['class'];
                    if ($c === $callerClass) {
                        break;
                    }
                    if (in_array($c, array_keys(self::$traces))) {
                        continue;
                    }
                    if (isset(self::$objDescFuncs[$c])) {
                        $idFunc = self::$objDescFuncs[$c];
                    } else {
                        $idFunc = create_function('$obj', 'return spl_object_hash($obj);');
                    }
                    $id = '<' . $idFunc($t['object']) . '>';
                } else {
                    $id = '';
                }
                $trace[] = sprintf('%s%s::%s()', $t['class'], $id, $t['function']);
            }
            $result[] = $trace;
        }

        return $result;
    }

    /**
     * Prints colour backtraces, signatures which are same in two subsequent
     * traces are printed in light gray colour.
     *
     * @param type $class
     * @param type $what
     */
    public static function printTraces($class, $what)
    {
        $lightGray = "0;37";
        $traces = self::getTraces($class, $what);
        $prevTrace = null;
        foreach ($traces as $traceIndex => $trace) {
            echo "\n{$class}::{$what}() call {$traceIndex}\n=>\n";
            foreach ($trace as $i => $signature) {
                $notChanged = false;
                if ($prevTrace) {
                    $prevOffset = count($prevTrace) - count($trace) + $i;
                    if (isset($prevTrace[$prevOffset])) {
                        $prevSignature = $prevTrace[$prevOffset];
                        $notChanged = $prevSignature === $signature;
                    }
                }
                if ($notChanged) {
                    print("\t{$i} => \033[{$lightGray}m{$signature}\033[0m\n");
                } else {
                    print("\t{$i} => {$signature}\n");
                }
            }
            $prevTrace = $trace;
        }
    }


    public static function reset($class = null)
    {
        if ($class !== null) {
            self::$counts[$class] = array();
        } else {
            self::$counts = array();
        }
    }
}

class CountableAuthor extends Author
{
    public $nbCallPreSave = 0;

    /**
     * {@inheritdoc}
     */
    public function preSave(PropelPDO $con = null)
    {
        $this->nbCallPreSave++;

        return parent::preSave($con);
    }

    /**
     * {@inheritdoc}
     */
    public function isDirtyWithRelated(array &$related = array())
    {
        $countWhat = __FUNCTION__ . '_' . (!$related ? 'Initial' : 'Recursive');
        Count::inc(__CLASS__, $countWhat);
        return parent::isDirtyWithRelated($related);
    }

    /**
     * {@inheritdoc}
     */
    public function isDirty()
    {
        Count::inc(__CLASS__, __FUNCTION__);
        return parent::isDirty();
    }
}

class CountableBook extends Book {
    /**
     * {@inheritdoc}
     */
    public function isDirtyWithRelated(array &$related = array())
    {
        $countWhat = __FUNCTION__ . '_' . (!$related ? 'Initial' : 'Recursive');
        Count::inc(__CLASS__, $countWhat);
        return parent::isDirtyWithRelated($related);
    }

    /**
     * {@inheritdoc}
     */
    public function isDirty()
    {
        Count::inc(__CLASS__, __FUNCTION__);
        return parent::isDirty();
    }
}

class CountableBookSummary extends BookSummary {
    /**
     * {@inheritdoc}
     */
    public function isDirtyWithRelated(array &$related = array())
    {
        $countWhat = __FUNCTION__ . '_' . (!$related ? 'Initial' : 'Recursive');
        Count::inc(__CLASS__, $countWhat);
        return parent::isDirtyWithRelated($related);
    }

    /**
     * {@inheritdoc}
     */
    public function isDirty()
    {
        Count::inc(__CLASS__, __FUNCTION__);
        return parent::isDirty();
    }
}

class CountableBookClubList extends BookClubList {
    /**
     * {@inheritdoc}
     */
    public function isDirtyWithRelated(array &$related = array())
    {
        $countWhat = __FUNCTION__ . '_' . (!$related ? 'Initial' : 'Recursive');
        Count::inc(__CLASS__, $countWhat);
        return parent::isDirtyWithRelated($related);
    }

    /**
     * {@inheritdoc}
     */
    public function isDirty()
    {
        Count::inc(__CLASS__, __FUNCTION__);
        return parent::isDirty();
    }
}
