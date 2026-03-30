</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  (function(){
    const backdrop = document.getElementById('apssLogoutBackdrop');
    const modal = backdrop?.querySelector('.apss-modal');
    const btnCancel = document.getElementById('apssCancelLogout');
    const btnConfirm = document.getElementById('apssConfirmLogout');
    let targetHref = null;

    function openModal(href){
      targetHref = href;
      backdrop?.classList.add('apss-backdrop-show');
      setTimeout(()=>modal?.classList.add('show'), 10);
    }

    function closeModal(){
      modal?.classList.remove('show');
      setTimeout(()=>backdrop?.classList.remove('apss-backdrop-show'), 150);
    }

    document.addEventListener('click', function(ev){
      const a = ev.target.closest('a.btn-logout');
      if (!a) return;
      ev.preventDefault();
      openModal(a.getAttribute('href') || '/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/logout.php');
    });

    btnCancel?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', (e)=>{ if (e.target === backdrop) closeModal(); });
    btnConfirm?.addEventListener('click', ()=>{
      const container = document.querySelector('.page-wrap') || document.querySelector('.container');
      if (container){ container.classList.remove('page-enter'); container.classList.add('page-exit'); }
      setTimeout(()=>{ window.location.href = targetHref || '/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/logout.php'; }, 200);
    });

    // fallback: jika modal tidak tersedia dan tombol logout diklik (misal style hilang), langsung logout
    document.addEventListener('click', function(ev){
      const a = ev.target.closest('a.btn-logout');
      if (!a) return;
      if (!backdrop || !modal || !btnConfirm) {
        ev.preventDefault();
        window.location.href = a.getAttribute('href') || '/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/logout.php';
      }
    });
  })();
</script>

<!-- Modal konfirmasi logout -->
<div id="apssLogoutBackdrop" class="apss-modal-backdrop" aria-hidden="true">
  <div class="apss-modal" role="dialog" aria-modal="true" aria-labelledby="apssLogoutTitle">
    <div class="apss-modal-header" id="apssLogoutTitle">Konfirmasi Logout</div>
    <div class="apss-modal-body">Yakin ingin keluar dari APSS?</div>
    <div class="apss-modal-actions">
      <button type="button" class="apss-btn apss-btn-cancel" id="apssCancelLogout">Batal</button>
      <button type="button" class="apss-btn apss-btn-confirm" id="apssConfirmLogout">Keluar</button>
    </div>
  </div>
</div>
</body>
</html>
