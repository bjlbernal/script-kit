#!/bin/sh

# current pwd
cur_path=`pwd`
# source path
src_path="$1"
# destination path
dest_path="$2"
# order file path
order_file_path=$3;

# catmincss.sh, checking that we have the css.order file.
css_order_file_path=false;
if [[ ${#order_file_path} == 0 ]]; then 
  for f in `ls $src_path`; do
    if [[ $f == 'css.order' ]]; then
      # set the css_order_file_path to the provided src_path
      css_order_file_path=$src_path;
    fi
  done
  
  if [[ $css_order_file_path == false ]]; then
    for f in `ls $cur_path`; do
      if [[ $f == 'css.order'  ]]; then
        # set the css_order_file_path to the cur_path
        css_order_file_path=${cur_path}/;
      fi
    done
  fi
fi

echo ${src_path}min.css

`echo '' > ${src_path}min.css`

while read f; do
  echo "cat ${src_path}${f} >> ${src_path}min.css"
  `cat $src_path$f >> ${src_path}min.css`
done <${css_order_file_path}css.order

