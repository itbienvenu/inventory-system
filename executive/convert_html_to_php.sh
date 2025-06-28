#!/bin/bash
# filepath: fix_html_references.sh

# Replace all .php with .php in all files, recursively
find . -type f -exec sed -i 's/\.php/\.php/g' {} +

echo "All .php references inside files have been changed to .php."