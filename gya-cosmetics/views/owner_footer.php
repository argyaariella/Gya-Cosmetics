</div><!-- end page-inner -->
  </div><!-- end page-content -->
</div><!-- end main-content -->

<div id="sidebarOverlay" onclick="toggleSidebar()"
  style="display:none;position:fixed;inset:0;background:rgba(26,5,18,.45);backdrop-filter:blur(4px);z-index:999;"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar(){
  const sb=document.getElementById('sidebar');
  const ov=document.getElementById('sidebarOverlay');
  if(window.innerWidth<=768){
    if(sb.classList.contains('open')){sb.classList.remove('open');ov.style.display='none';}
    else{sb.classList.add('open');ov.style.display='block';}
  }
}
window.addEventListener('resize',function(){
  if(window.innerWidth>768){
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').style.display='none';
  }
});
document.addEventListener('DOMContentLoaded',function(){
  const area=document.getElementById('alertArea');
  const alerts=area?area.querySelectorAll('.alert-gya'):[];
  if(alerts.length>0){
    area.style.display='block';
    alerts.forEach(function(a){
      setTimeout(function(){
        a.style.transition='opacity .5s,transform .5s';
        a.style.opacity='0';
        a.style.transform='translateY(-8px)';
        setTimeout(()=>{a.remove();area.style.display='none';},500);
      },4000);
    });
  }
});
function confirmDelete(msg){return confirm(msg||'Yakin hapus data ini?');}
function openModal(id){const m=document.getElementById(id);if(!m)return;m.classList.add('show');document.body.style.overflow='hidden';}
function closeModal(id){const m=document.getElementById(id);if(!m)return;m.classList.remove('show');document.body.style.overflow='';}
document.addEventListener('click',function(e){
  if(e.target.classList.contains('modal-overlay')){
    const m=e.target.closest('.modal-gya');
    if(m){m.classList.remove('show');document.body.style.overflow='';}
  }
});
document.addEventListener('keydown',function(e){
  if(e.key==='Escape'){
    document.querySelectorAll('.modal-gya.show').forEach(function(m){
      m.classList.remove('show');document.body.style.overflow='';
    });
  }
});
</script>
<?php if(isset($extra_js)) echo $extra_js; ?>
</body>
</html>