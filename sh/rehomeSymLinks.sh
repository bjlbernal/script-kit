#!/bin/sh

# declaring variables
SYM_LINK_FILE='symLinks'
CURRENT_PATH=`pwd -P`
OLD_PATH='/var/chroot/home/content///html'

`ls -laR ~/www/* | grep '^lr\|:$' > $SYM_LINK_FILE`

#SYM_LINK_SRC=`cat $SYM_LINK_FILE`
#echo "$SYM_LINK_SRC"

while read line; do
  sym_link_file=''
  sym_link_path=''

  if [[ $line =~ :$ ]] ; then
    dir_path=$CURRENT_PATH${line:16}
    dir_path=${dir_path/:/}
  elif [[ $line =~ ^lr ]] ; then
    arr=($line)
    sym_link_file=${arr[8]}
    sym_link_path=${arr[10]}
    sym_link_path=${sym_link_path/$OLD_PATH/'~/www'}

#   Exclude parent directory paths
    if [[ ${sym_link_path:0:2} =~ '..' ]] ; then
      sym_link_path=''
    elif [[ ${sym_link_path:0:1} =~ '/' ]] ; then
      sym_link_path=''
    fi
  fi

  sym_link_path=${sym_link_path/'~/www'/$CURRENT_PATH'/www'}

  if [ -n "$sym_link_path" ] ; then
    TARGET=$sym_link_path
    DIRECTORY=$dir_path'/'$sym_link_file
    echo "ln -sf $TARGET $DIRECTORY"
    `ln -sf $TARGET $DIRECTORY`
  fi
done < $SYM_LINK_FILE

