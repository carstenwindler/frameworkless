#!/usr/bin/env bash

set -o pipefail  # trace ERR through pipes
set -o errtrace  # trace ERR through 'time command' and other functions
set -o nounset   ## set -u : exit the script if you try to use an uninitialised variable
set -o errexit   ## set -e : exit the script if any statement returns a non-true return value

source "$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/.util.sh"

mkdir -p -- "${BACKUP_DIR}"

if [[ -n "$(dockerContainerId mysql)" ]]; then
    if [ -f "${BACKUP_DIR}/${BACKUP_MYSQL_FILE}" ]; then
        logMsg "Removing old backup file..."
        rm -f -- "${BACKUP_DIR}/${BACKUP_MYSQL_FILE}"
    fi

    logMsg "Starting MySQL backup..."
    MYSQL_ROOT_PASSWORD=$(dockerExecMySQL printenv MYSQL_ROOT_PASSWORD)
    dockerExecMySQL sh -c "MYSQL_PWD=\"${MYSQL_ROOT_PASSWORD}\" mysqldump -h mysql -uroot --opt --single-transaction --events --all-databases --routines --comments" | bzip2 > "${BACKUP_DIR}/${BACKUP_MYSQL_FILE}"
    logMsg "Finished"
else
    logMsg "Skipping mysql backup, no such container"
fi
