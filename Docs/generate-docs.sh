#!/bin/bash
SELF=$(cd $(dirname $0); pwd -P)/$(basename $0)
SELF_DIR=$(dirname $SELF)

PHPDOC=$SELF_DIR/phpDoc
DOXYGEN=$SELF_DIR/Doxygen
DOCBLOX=$SELF_DIR/DocBlox

SOURCE=$(readlink -f $SELF_DIR/../lib/SiTech)

for arg in "$*";
do
	if [ "$arg" = "--clean" ]; then
		echo -ne "Cleaning..."
		rm -Rf ${PHPDOC} ${DOXYGEN} ${DOCBLOX}/*
		echo "done."
	fi

	if [ "$arg" = "--phpdoc" ]; then
		if [ ! -d "$PHPDOC" ]; then
			mkdir "$PHPDOC"
		fi
		phpdoc -d ${SOURCE} -t ${PHPDOC} -o HTML:frames:earthli -s on -ti 'SiTech Documentation' -dn SiTech -dc SiTech
	elif [ "$arg" = "--doxygen" ]; then
		if [ ! -d "$DOXYGEN" ]; then
			mkdir "$DOXYGEN"
		fi
		doxygen $SELF_DIR/SiTech.doxygen
	elif [ "$arg" = "--docblox" ]; then
		if [ ! -d "$DOCBLOX" ]; then
			mkdir "$DOCBLOX"
		fi
		docblox project:parse -d ${SOURCE} -t ${DOCBLOX}
		docblox project:transform project:transform -s ${DOCBLOX}/structure.xml -t ${DOCBLOX}
	fi
done
