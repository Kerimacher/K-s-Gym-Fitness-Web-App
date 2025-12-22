document.addEventListener('DOMContentLoaded', function() {
    const programIstemeButonlari = document.querySelectorAll('.program-iste-btn');

    programIstemeButonlari.forEach(function(buton) {
        buton.addEventListener('click', function() {
            const diyetisyenId = this.dataset.diyetisyenId; 
            
            console.log("Program istenen diyetisyen ID:", diyetisyenId);

           
            fetch('program_gonder.php', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'diyetisyen_id=' + encodeURIComponent(diyetisyenId) 
            })
            .then(response => {
                if (!response.ok) {
                    
                    throw new Error('Sunucu yanıtı sorunlu: ' + response.status); 
                }
                return response.json(); 
            })
            .then(data => {
                console.log('PHP Yanıtı:', data); 
                if (data.success) {
                    alert('Başarılı: ' + data.message); 
                } else {
                    alert('Bilgi: ' + data.message); 
                }
            })
            .catch(error => {
                console.error('Fetch veya işleme hatası:', error);
                alert('İstek gönderilirken bir sorun oluştu. Detaylar için konsolu kontrol edin.');
            });
            
        });
    });
});