// Auto-init when DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Barang Masuk Form
    initBarangMasukForm();
    
    // Barang Keluar Form
    initBarangKeluarForm();
    
    // Dashboard Chart
    initDashboardChart();
});

function initBarangMasukForm() {
    const barangSelect = document.getElementById('barang_id');
    if (!barangSelect) return;
    
    const barangs = window.appData?.barangs || [];
    
    barangSelect.addEventListener('change', function() {
        const barangId = this.value;
        const unitSelect = document.getElementById('unit_name');
        unitSelect.innerHTML = '<option value="">Pilih Satuan</option>';
        
        if (barangId) {
            const barang = barangs.find(b => b.id == barangId);
            if (barang?.item_units) {
                barang.item_units.forEach(unit => {
                    const option = document.createElement('option');
                    option.value = unit.unit_name;
                    option.textContent = unit.unit_name + (unit.is_base ? ' (base)' : ` (1 = ${unit.multiplier} ${barang.base_unit})`);
                    unitSelect.appendChild(option);
                });
            }
        }
    });
    
    // Calculate total
    ['jumlah', 'harga_beli'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function() {
                const jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
                const harga = parseFloat(document.getElementById('harga_beli').value) || 0;
                const total = jumlah * harga;
                document.getElementById('total_harga').value = total.toLocaleString('id-ID');
            });
        }
    });
}

function initBarangKeluarForm() {
    // Similar implementation...
}

function initDashboardChart() {
    const canvas = document.getElementById('profitChart');
    if (!canvas) return;
    
    const chartData = window.appData?.chartData || { labels: [], data: [] };
    
    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Profit/Loss (Rp)',
                data: chartData.data,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: { /* ... */ }
    });
}