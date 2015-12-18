#!/bin/sh

# current pwd
cur_path=`pwd`
# source path
src_path="$1"
# destination path
dest_path="$2"
# order file path
order_file_path=$3;


# sync_dev2www.sh, checking that we have the dev and www directory.
dev_dir_press=false;
www_dir_press=false;
for f in `ls $cur_path`; do
  if [[ $f == 'dev' ]]; then
    dev_dir_press=true;
  fi

  if [[ $f == 'www' ]]; then
    echo $f;
    www_dir_press=true;
  fi
done


# catmincss.sh, checking that we have the css.order file.
css_order_file_path=false;
if [[ ${#order_file_path} == 0 ]]; then 
  for f in `ls $src_path`; do
    if [[ $f == 'css.order' ]]; then
      css_order_file_path=$src_path;
    fi
  done
  
  if [[ $css_order_file_path == false ]]; then
    for f in `ls $cur_path`; do
      if [[ $f == 'css.order'  ]]; then
        css_order_file_path=$cur_path/;
      fi
    done
  fi
fi


# catminjs.sh, checking that we have the js.order file.
js_order_file_path=false;
if [[ ${#order_file_path} == 0 ]]; then 
  for f in `ls $src_path`; do
    if [[ $f == 'js.order' ]]; then
      js_order_file_path=$src_path;
    fi
  done
  
  if [[ $js_order_file_path == false ]]; then
    for f in `ls $cur_path`; do
      if [[ $f == 'js.order'  ]]; then
        js_order_file_path=$cur_path/;
      fi
    done
  fi
fi

echo ""
echo "cur_path $cur_path"
echo "length ${#cur_path}"
echo "dev_dir_press $dev_dir_press"
echo "www_dir_press $www_dir_press"
echo "order_file_path $order_file_path"
echo "length ${#order_file_path}"
echo "css_order_file_path $css_order_file_path"
echo "js_order_file_path $js_order_file_path"
echo "src_path $src_path"
echo "length ${#src_path}"
echo "dest_path $dest_path"
echo "length ${#dest_path}"
 
