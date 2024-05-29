<footer class="main-footer">
    <div class="footer-left">
        Copyright &copy; <span id="current-year"></span> <div class="bullet"></div> Toko Berkah
    </div>
</footer>

<script>
    // Mengambil elemen dengan ID 'current-year'
    const yearSpan = document.getElementById('current-year');
    
    // Mendapatkan tahun saat ini
    const currentYear = new Date().getFullYear();
    
    // Menetapkan tahun saat ini ke elemen
    yearSpan.textContent = currentYear;
</script>
