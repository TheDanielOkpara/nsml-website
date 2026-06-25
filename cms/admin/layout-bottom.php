  </main>
</div>

<div class="modal-overlay" id="confirmOverlay">
  <div class="modal-box">
    <h3 id="confirmTitle">Are you sure?</h3>
    <p id="confirmMessage"></p>
    <div class="modal-actions">
      <button type="button" class="btn secondary sm" id="confirmCancel">Cancel</button>
      <button type="button" class="btn danger sm" id="confirmOk">Delete</button>
    </div>
  </div>
</div>

<script>
(function () {
  var toggle = document.getElementById('navToggle');
  if (toggle) {
    toggle.addEventListener('click', function () {
      var collapsed = document.documentElement.classList.toggle('nav-collapsed');
      localStorage.setItem('nsmlNavCollapsed', collapsed ? '1' : '0');
    });
  }

  var overlay = document.getElementById('confirmOverlay');
  var titleEl = document.getElementById('confirmTitle');
  var msgEl = document.getElementById('confirmMessage');
  var okBtn = document.getElementById('confirmOk');
  var cancelBtn = document.getElementById('confirmCancel');
  var pendingHref = null;

  function closeModal() { overlay.classList.remove('open'); pendingHref = null; }

  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-confirm]');
    if (!el) return;
    e.preventDefault();
    titleEl.textContent = el.getAttribute('data-confirm-title') || 'Are you sure?';
    msgEl.textContent = el.getAttribute('data-confirm');
    okBtn.textContent = el.getAttribute('data-confirm-ok') || 'Delete';
    pendingHref = el.getAttribute('href');
    overlay.classList.add('open');
  });

  okBtn.addEventListener('click', function () {
    if (pendingHref) window.location.href = pendingHref;
  });
  cancelBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', function (e) { if (e.target === overlay) closeModal(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });
})();
</script>
</body>
</html>
