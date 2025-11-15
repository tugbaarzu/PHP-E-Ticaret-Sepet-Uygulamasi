# 🛒 E-Ticaret Sepet Uygulaması

Bir e-ticaret sepet uygulaması. PHP ile geliştirilmiş, JSON dosyası kullanarak veri saklayan hafif bir web uygulaması.

## ✨ Özellikler

- ✅ Ürün listeleme ve görüntüleme
- ✅ Sepete ürün ekleme
- ✅ Sepet görüntüleme ve yönetimi
- ✅ Miktar güncelleme
- ✅ Sepetten ürün çıkarma
- ✅ Sepet temizleme
- ✅ KDV hesaplama (%18)
- ✅ Toplam fiyat hesaplama
- 🎨 Modern ve responsive tasarım
- 💾 JSON dosyası ile veri saklama
- 📱 Mobil uyumlu arayüz

## 📋 Gereksinimler

- PHP 7.0 veya üzeri
- Web sunucusu (Apache, Nginx, veya PHP built-in server)


## 📁 Proje Yapısı

```
php-ecommerce/
├── index.php          # Ana sayfa (ürün listesi)
├── cart.php           # Sepet sayfası
├── css/
│   └── style.css      # Stil dosyası
├── js/
│   └── main.js        # JavaScript dosyası
├── data/              # Veri klasörü (otomatik oluşturulur)
│   ├── products.json  # Ürün verileri
│   └── cart.json      # Sepet verileri
├── .gitignore         # Git ignore dosyası
└── README.md          # Bu dosya
```

## 🛠️ Kullanım

### Ürün Ekleme
1. Ana sayfada ürünleri görüntüleyin
2. İstediğiniz ürünün miktarını seçin
3. "Sepete Ekle" butonuna tıklayın

### Sepet Yönetimi
1. "Sepetim" linkine tıklayın
2. Ürün miktarını güncelleyebilirsiniz
3. Ürünleri sepetten çıkarabilirsiniz
4. Sepeti tamamen temizleyebilirsiniz
5. Sipariş özetini görüntüleyebilirsiniz

## 📝 Özellikler Detayı

### Ürün Özellikleri
- Ürün adı
- Açıklama
- Fiyat
- Stok miktarı
- Görsel (emoji)

### Sepet Özellikleri
- Ürün listesi
- Miktar güncelleme
- Ara toplam
- KDV hesaplama (%18)
- Genel toplam
- Sepet temizleme

## 🔒 Güvenlik

- XSS koruması için `htmlspecialchars()` kullanılmıştır
- Form verileri doğrulanmaktadır
- Session kullanarak mesaj gösterimi
- Üretim ortamında ek güvenlik önlemleri alınmalıdır

