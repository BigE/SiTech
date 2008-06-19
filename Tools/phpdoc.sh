#!/bin/bash
# Generate documentation based on the code.

phpdoc -d lib -t Docs/Generated -ti 'SiTech Project' -dn 'SiTech' -o HTML:Smarty:PHP -pp
