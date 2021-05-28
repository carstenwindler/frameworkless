#!/usr/bin/env bash

set -o pipefail  # trace ERR through pipes
set -o errtrace  # trace ERR through 'time command' and other functions
set -o nounset   ## set -u : exit the script if you try to use an uninitialised variable
set -o errexit   ## set -e : exit the script if any statement returns a non-true return value

source "$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/.util.sh"

if [ "$#" -lt 1 ]; then
    errorMsg "No file specified"
    exit 1
fi

MYSQL_FILE=$1

mkdir -p -- "${BACKUP_DIR}"

if [[ -n "$(dockerContainerId mysql)" ]]; then
    if [ -f "${MYSQL_FILE}" ]; then
        logMsg "Importing MySQL dump ${MYSQL_FILE}"
        MYSQL_ROOT_PASSWORD=$(dockerExecMySQL printenv MYSQL_ROOT_PASSWORD)
        MYSQL_DATABASE=$(dockerExecMySQL printenv MYSQL_DATABASE)
        cat "${MYSQL_FILE}" | dockerExecMySQL sh -c "MYSQL_PWD=\"${MYSQL_ROOT_PASSWORD}\" mysql ${MYSQL_DATABASE} -h mysql -uroot"
        echo "FLUSH PRIVILEGES;" | dockerExecMySQL sh -c "MYSQL_PWD=\"${MYSQL_ROOT_PASSWORD}\" mysql ${MYSQL_DATABASE} -h mysql -uroot"
        logMsg "Finished"
    else
        errorMsg "MySQL dump ${MYSQL_FILE} not found"
        exit 1
    fi
else
    logMsg "Skipping mysql import, no such container"
fi
