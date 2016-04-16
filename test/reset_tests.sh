#!/usr/bin/env bash
# Reset Propel tests fixtures
# 2011 - William Durand <william.durand1@gmail.com>

if [ "$1" = "-h" -o "$1" = "--help" ]; then
cat <<EOT
$(basename $0) [OPTIONS]
Reset Propel tests fixtures

Options:
    -v      Be verbose: Prints propel-gen commands being executed.
    -vv     Be more verbose: Prints propel-gen commands output.
EOT
    exit
fi

VERBOSE=$(echo "$*" | grep -Ewo -e '-v+' | cut -d- -f2 | tr -d '\n' | wc -c)

FIXTURES_DIR="$(dirname $0)/fixtures";
ROOT_DIR="$(realpath -e "$(dirname $0)/..")"

if [ ! -d "$FIXTURES_DIR" ] ; then
    echo "ERROR: {$FIXTURES_DIR} directory not found !" >&2
    exit 1
fi
if [ -z "$ROOT_DIR" ] ; then
    echo "ERROR: Propel1 root directory not found !" >&2
    exit 1
fi

function gen
{
    local cmd="$ROOT_DIR/generator/bin/propel-gen $*"
    [ $VERBOSE -ge 1 ] && echo "    $cmd"
    [ $VERBOSE -ge 2 ] && $cmd || $cmd >/dev/null
}

function rebuild
{
    local dir=$1

    if [ ! -f "$FIXTURES_DIR/$dir/build.properties" ]; then
        return
    fi

    echo "[ $dir ]"

    if [ -d "$FIXTURES_DIR/$dir/build" ] ; then
        rm -rf "$FIXTURES_DIR/$dir/build"
    fi

    gen $FIXTURES_DIR/$dir main
    gen $FIXTURES_DIR/$dir insert-sql
}

DIRS=`ls $FIXTURES_DIR`

for dir in $DIRS ; do
    rebuild $dir
done

# Special case for reverse fixtures

REVERSE_DIRS=`ls $FIXTURES_DIR/reverse`

for dir in $REVERSE_DIRS ; do
    if [ -f "$FIXTURES_DIR/reverse/$dir/build.properties" ] ; then
        echo "[ reverse / $dir ]"
        gen $FIXTURES_DIR/reverse/$dir insert-sql
    fi
done
