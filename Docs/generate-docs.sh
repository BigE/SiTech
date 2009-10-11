#!/bin/bash
SELF=$(cd $(dirname $0); pwd -P)/$(basename $0)
SELF_DIR=$(dirname $SELF)
GENERATED=$SELF_DIR/Generated
SOURCE=$(readlink -f $SELF_DIR/../lib/SiTech)

if [ $1 = "clean" ]; then
	echo -ne "Cleaning..."
	rm -Rf ${GENERATED}/*
	echo "done."
fi
phpdoc -d ${SOURCE} -t ${GENERATED} -o HTML:frames:earthli -s on -ti 'SiTech Documentation' -dn SiTech -dc SiTech
