// Toggle Favorit - Load di AKHIR index.php setelah jQuery dan DataTables ready
$(document).ready(function() {
    console.log('Toggle Favorit Script Loaded');
    
    // Setup jQuery untuk mengirim cookies dengan AJAX
    $.ajaxSetup({
        xhrFields: {
            withCredentials: true
        }
    });

    // Event handler untuk toggle favorit
    // Gunakan delegated event untuk kompatibilitas dengan DataTables
    $(document).on('click', '.toggle-favorit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var id_buku = $(this).data('id');
        var btn = $(this);
        
        console.log('Tombol favorit diklik untuk buku ID: ' + id_buku);
        
        // Validasi
        if (!id_buku) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'ID Buku tidak ditemukan'
            });
            return false;
        }
        
        // AJAX request
        $.ajax({
            url: 'admin/siswa/toggle_favorit.php',
            type: 'POST',
            data: { id_buku: id_buku },
            dataType: 'json',
            timeout: 5000,
            success: function(response) {
                console.log('Response:', response);
                
                if (response.status === 'added') {
                    btn.removeClass('btn-default btn-secondary')
                       .addClass('btn-danger');
                    btn.find('i').removeClass('fa-heart-o').addClass('fa-heart');
                    btn.html('<i class="fa fa-heart"></i> Hapus');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Buku ditambahkan ke favorit',
                        timer: 2000
                    });
                } else if (response.status === 'removed') {
                    btn.removeClass('btn-danger')
                       .addClass('btn-default btn-secondary');
                    btn.find('i').removeClass('fa-heart').addClass('fa-heart-o');
                    btn.html('<i class="fa fa-heart-o"></i> Favorit');
                    
                    Swal.fire({
                        icon: 'info',
                        title: 'Berhasil!',
                        text: 'Buku dihapus dari favorit',
                        timer: 2000
                    });
                } else if (response.status === 'session_error') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sesi Expired',
                        text: 'Silakan login ulang',
                        willClose: function() {
                            window.location = 'index.php?page=logout';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Terjadi kesalahan'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('Error Status:', status);
                console.log('Error:', error);
                console.log('Response:', xhr.responseText);
                
                var errorMsg = 'Terjadi kesalahan';
                if (status === 'timeout') {
                    errorMsg = 'Request timeout - server lambat';
                } else if (xhr.status === 404) {
                    errorMsg = 'File toggle_favorit.php tidak ditemukan';
                } else if (xhr.status === 500) {
                    errorMsg = 'Error server - cek console';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMsg + ' (' + status + ')'
                });
            }
        });
        
        return false;
    });
});
