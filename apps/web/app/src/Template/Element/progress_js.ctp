<script>

/**
 * 
 * BUILDデータのプログレス対応
 * 
 */
loopForProgress();
setInterval(loopForProgress, 1000);

/**
 * 
 * 
 * 
 */
function loopForProgress()  {
  const builddonloads = document.querySelectorAll(".builddonload");
  builddonloads.forEach(element => {
    putProgress(element);
  });
}


/**
 * 
 * プログレスラベル対応
 * 
 */
async function putProgress(element){
  const id = element?.dataset?.id;
  const progress = await getZipProgress(id);
  if(progress === 0){
    element.hidden = true;
    return;
  }else{
    element.hidden = false;
  }

  if(progress === 100){
    element.textContent = "ダウンロード";
    element.classList.remove("disabled");
    return;
  }

  element.textContent = "ファイル生成中: " + progress + "%";
  element.classList.add("disabled");
  return;
}

/**
 * 
 * プログレス取得
 * 
 */
async function getZipProgress(id){
  const res = await fetch('/v1/views/progress-build.json?id='+id);
  const jso = await res.json();
  const progress = jso?.data?.progress;
  return progress;
}
</script>