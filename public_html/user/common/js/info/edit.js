var sortable_option = {
    items:'tr.first-dir',
    placeholder: 'ui-state-highlight',
    opacity: 0.7,
    handle:'td div.sort_handle',
    connectWith: '.list_table_sub',
    update: function(e, obj) {

    }
};

var sortable_option_sub = {
    items:'tr.first-dir',
    placeholder: 'ui-state-highlight',
    opacity: 0.7,
    handle:'td div.sort_handle',
    connectWith: '.list_table, .list_table_sub',
    receive: function(e, obj) {
      var waku_block_type = $(this).data('wakuBlockType');
      var section_no = $(this).closest('table').data('sectionNo');
      Object.keys(out_waku_list).forEach(function (k) {
          if (waku_block_type == k) {
            for(let i in out_waku_list[k]) {
              if ($(obj.item['context']).find('input.block_type').val() == out_waku_list[k][i]) {
                if (obj.sender !== null) {
                  $(obj.sender.context).sortable('cancel');
                }
                return false;
              }
            }
          }
        });
    },
    update: function(e, obj) {
      var section_no =$(obj.item['context'].closest('table')).data('sectionNo');
      if (typeof section_no !== 'undefined') {
        var waku_block_type = $(obj.item['context'].closest('table')).data('blockType');
        $(obj.item['context']).find('input.section_no').val(section_no);
      } else if (obj.sender == null) {
        $(obj.item['context']).find('input.section_no').val(0);
      }
    }
};

var sortable_option_relation = {
    items:'tr.relation-dir',
    placeholder: 'ui-state-highlight',
    opacity: 0.7,
    handle:'td div.sort_handle',
    receive: function(e, obj) {
      var waku_block_type = $(this).data('wakuBlockType');
      var section_no = $(this).closest('table').data('sectionNo');
      Object.keys(out_waku_list).forEach(function (k) {
          if (waku_block_type == k) {
            for(let i in out_waku_list[k]) {
              if ($(obj.item['context']).find('input.block_type').val() == out_waku_list[k][i]) {
                if (obj.sender !== null) {
                  $(obj.sender.context).sortable('cancel');
                }
                return false;
              }
            }
          }
        });
    },
    update: function(e, obj) {

    }
};

function addTag(tag) {
  var url = '/user/infos/add_tag';
  var params = {
    'num' : tag_num,
    'tag' : tag
  };

  $.post(url, params, function(a) {
    $("#tagArea").append(a);
    tag_num++;
  });
}

function addBlock(type) {
  var url = '/user/infos/add_row';
  var params = {
    'rownum':rownum,
    'block_type' : type
  };

  if (rownum >= max_row) {
    alert_dlg(`追加できるブロックは${max_row}件までです。`);
    return;
  }
  $.post(url, params, function(a) {
    $("#blockArea").append(a);
    if (type == 2 || type == 11) {
      var elm = `#block_no_${rownum} textarea.editor`;
      setWysiwyg(elm);
    }

    if (type == 13) {
      $(`#block_no_${rownum} .list_table_relation`).sortable(sortable_option_relation);
    }
    else if (type in block_type_waku_list !== false) {
      $(`#block_no_${rownum} .list_table_sub`).sortable(sortable_option_sub);
    }

    rownum++;
  });
}

// 関連記事枠の関連記事　専用
function addBlockRelation(waku_no, section_no) {
  var type = block_type_relation;
  var url = '/user/infos/add_row';
  var params = {
    'rownum':rownum,
    'block_type' : type,
    'section_no' : section_no
  };

  if (rownum >= max_row) {
    alert_dlg(`追加できるブロックは${max_row}件までです。`);
    return;
  }
  $.post(url, params, function(a) {
    $(`#block_no_${waku_no} .list_table_relation`).append(a);

    rownum++;
  });
}

function setWysiwyg(elm) {
  $R(elm, {
      focus: true,
      minHeight: '200px',
      // imageUpload: '/user/info_images/image_upload.json',
      // imageManagerJson: '/user/info_images/image_list.json',
      //imagePosition: true,
      //imageResizable: true,
      multipleUpload: false,
      plugins: ['fontsize','fontcolor','counter','alignment'],
      // buttons: [ 'html', 'formatting', 'bold', 'italic', 'deleted',
      //  'orderedlist', 
      //  'link', 
      //  'alignment',
      //  'horizontalrule'],
      buttonsHide: ['format'],
      lang: 'ja',
      pastePlainText: true,
      //breakline: true,
      //markup: 'br' ,
      buttonsAddAfter: {
        after: 'deleted',
        buttons: ['underline']
      }
    });
}

function changeStyle(elm, rownum, target_class, target_name) {
  var class_name = $(elm).val();

  $(`#block_no_${rownum} .${target_class}`).removeClass(function(index, className){
    var match = new RegExp("\\b" + target_name + "\\S+","g");
    return (className.match(match) || []).join(' ');
  });

  if (class_name != "") { 
    if (class_name.match(/^[0-9]+$/)) {
      class_name = target_name + class_name;
    }
    $(`#block_no_${rownum} .${target_class}`).addClass(class_name);
  }

  var type = $(elm).closest('tbody.list_table_sub').data('wakuBlockType');
  if (type in block_type_waku_list !== false) {
      if (class_name == 'waku_style_6') {
        $(`#block_no_${rownum} .optionValue3`).attr('disabled', true);
        $(`#block_no_${rownum} .optionValue3`).val('');
      } else {
        // $(`#block_no_${rownum} .optionValue2`).attr('disabled', false);
        $(`#block_no_${rownum} .optionValue3`).attr('disabled', false);
      }
    }
}

function changeSelectStyle(elm, rownum) {
  var style = $(elm).val();
  // console.log(style);
  if (style == 'waku_style_6') {
    $("#idWakuColorCol_" + rownum).hide();
    $("#idWakuColorCol_" + rownum + ' select').attr("disabled", true);
    $("#idWakuBgColorCol_" + rownum ).show();
    $("#idWakuBgColorCol_" + rownum + ' select').attr("disabled", false);
  } else {
    $("#idWakuBgColorCol_" + rownum ).hide();
    $("#idWakuBgColorCol_" + rownum + ' select').attr("disabled", true);
    $("#idWakuColorCol_" + rownum).show();
    $("#idWakuColorCol_" + rownum + ' select').attr("disabled", false);
  }
}

function changeWidth(elm, rownum, target_class, name) {
  var num = $(elm).val();

  if (num > 0) {
    $(`#block_no_${rownum} .${target_class}`).css(name, `${num}px`);
  } else {
    $(`#block_no_${rownum} .${target_class}`).css(name, ``);
    num = '';
    $(elm).val(num);
  }
}
function getFileSize(e) {

    var file = e.files[0];

    if (typeof file === 'undefined') {
      return 0;
    }
    if (file.length == 0) {
      return 0;
    }

    var reader = new FileReader();

    if (file.size > max_file_size) {
        alert_dlg('１ファイルのアップロード出来る容量を超えています');
        $(e).val('');
        return 0;
    }

    return file.size;
}
$(function () {
    rownum = $("#idContentCount").val();

    $("body").on('change', '.attaches', function() {
//       var id = $(this).attr('id');
// console.log(id);
//       var ele = document.getElementById(id);
// console.log(ele);
//       var size = getFileSize(document.getElementById(id));
//       if (size > max_file_size) {
//         alert_dlg('１ファイルのアップロード出来る容量を超えています');
//         $(this).val('');
//       }

      var attaches = document.getElementsByClassName('attaches');
      form_file_size = 0;
      for (var i = 0; i < attaches.length; i++) {
        form_file_size += getFileSize(attaches[i]);
      }  

      if (form_file_size > total_max_size) {
        $(this).val('');
        alert_dlg('一度にアップ出来る容量を超えました。一度保存してください');
        return;
      }
      // console.log(form_file_size);
    });

    

  // 並び替え
    $(".list_table").sortable(sortable_option);
    $(`.list_table_sub`).sortable(sortable_option_sub);
    $(`.list_table_relation`).sortable(sortable_option_relation);


    // ブロック削除
    $('body #blockArea').on('click', '.btn_list_delete', function(){
        var row = $(this).data("row");
        var block_id = $(`#idBlockId_${row}`).val();

        $(`#block_no_${row} input, #block_no_${row} textarea, #block_no_${row} select`).attr('disabled', true);
        $(`#block_no_${row}`).addClass('delete');
        $(this).html('元に戻す');
        $(this).removeClass('btn_list_delete');
        $(this).addClass('btn_list_undo');


        if (block_id > 0) {
          var html = `<input type="hidden" name="delete_ids[]" value="${block_id}" id="delBlock_${block_id}">`;
          $("#deleteArea").append(html);
        }
    });

    // 削除を元に戻す
    $('body #blockArea').on('click', '.btn_list_undo', function(){
        var row = $(this).data("row");
        var block_id = $(`#idBlockId_${row}`).val();

        $(`#block_no_${row} input, #block_no_${row} textarea, #block_no_${row} select`).attr('disabled', false);
        $(`#block_no_${row}`).removeClass('delete');
        $(this).html('削除');
        $(this).removeClass('btn_list_undo');
        $(this).addClass('btn_list_delete');


        if (block_id > 0) {
          $(`#deleteArea #delBlock_${block_id}`).remove();
        }
    });

    // タグ追加
    $('#btnAddTag').on('click', function() {
      var tag = $("#idAddTag").val();
      if (tag != "") {
        addTag(tag);
        $("#idAddTag").val('');
      }
      return false;
    });

    // タグ削除
    $("#tagArea").on('click', '.delete_tag', function() {
      var id = $(this).data('id');
      $("#tag_id_" + id).addClass('delete');
      $("#tag_id_" + id + ' input').attr('disabled', true);
      $("#tag_id_" + id + ' a').removeClass('delete_tag');
      $("#tag_id_" + id + ' a').addClass('delete_rollbak');
    });

    // タグ削除取消
    $("#tagArea").on('click', '.delete_rollbak', function() {
      var id = $(this).data('id');
      $("#tag_id_" + id).removeClass('delete');
      $("#tag_id_" + id + ' input').attr('disabled', false);
      $("#tag_id_" + id + ' a').removeClass('delete_rollbak');
      $("#tag_id_" + id + ' a').addClass('delete_tag');
    });

    // タグリスト
    $("#btnListTag").on('click', function() {
      pop_box.select = function(tag) {
        addTag(tag);
        pop_box.close();
      };

      pop_box.open({
            element: "#btnListTag",
            href: "/user/infos/pop_taglist?page_config_id=" + page_config_id,
            open: true,
            onComplete: function(){
            },
            onClosed: function() {
                pop_box.remove();
            },
            opacity: 0.5,
            iframe: true,
            width: '900px',
            height: '750px'
          });

          return false;
    })

    $("body").on('click', '.pop_image_single', function(){
        pop_box.image_single();
    });

    // redactor
    setWysiwyg('#blockTable textarea.editor');

});