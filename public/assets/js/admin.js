function deleteProduct(id) {
    if (!confirm('Bạn có chắc muốn ẩn sản phẩm này?')) return;

    fetch(APP_URL + '/admin/products/' + id + '/delete', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
                return;
            }
            alert(data.message || 'Không thể cập nhật sản phẩm.');
        })
        .catch(() => alert('Không thể kết nối đến máy chủ.'));
}
