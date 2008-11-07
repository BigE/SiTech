#!/bin/bash
# Generate documentation based on the code.

TOOLS_PATH=$(dirname $(readlink -f $0))
BASE_PATH=$(readlink -f "${TOOLS_PATH}/../")
phpdoc -d "${BASE_PATH}/lib" -t "${BASE_PATH}/Docs/Generated" -ti 'SiTech Project' -dn 'SiTech' -o HTML:Smarty:PHP -pp
