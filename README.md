# HTML5Up Hyperspace

Bu proje, basit bir kişisel portfolyo projesidir.

## 🚀 Özellikler:
- HTML5 ve CSS3 ile modern tasarım.
- Duyarlı (Responsive) arayüz.
- PHP ile dinamik içerik yönetimi.

## 📦 Teknolojiler:
 - HTML5 / CSS3 / JavaScript
 - PHP
 - MySQL (phpMyAdmin)

## 🛠 Kurulum:
Projeyi yerel bilgisayarınızda çalıştırmak için şu adımları izleyin:

1. **Repoyu Klonlayın:**
   ```
   git clone https://github.com/oguz00000/html5up-hyperspace.git
   ```

2. **Ortam Ayarlarını Yapın:**
 - Proje klasöründeki .env.example dosyasını kopyalayın ve adını .env olarak değiştirin.
 - .env dosyasını bir metin editörüyle açın ve veritabanı bilgilerinizi (DB_NAME, DB_USER, DB_PASS) kendi yerel ortamınıza göre güncelleyin.

3. **Veritabanını Hazırlayın:**
 - phpMyAdmin'e yada farklı bir veritabanı yönetim aracına (MySQL ile çalışabilmesi gerekiyor) girin.
 - yeni bir veritabanını manuel olarak oluşturmanıza gerek yok, direkt olarak sql koduyla proje için gerekli her şeyi oluşturacaktır.

4. **Konfigürasyon:**
 - config.php dosyasını kendi veritabanı bilgilerinize göre güncelleyin.

5. **Güvenlik Notu:**
 - Bu projede admin girişi için `password_verify` fonksiyonu kullanılmaktadır. Veritabanınızda şifreleri "açık metin" (plain text) olarak tutmak yerine, PHP'nin `password_hash()` fonksiyonu ile şifrelenmiş (hash) hallerini saklamanız gerekmektedir. Admin şifrenizi ayarlarken bu standartı kullandığınızdan emin olun.

## 👤 İletişim:
 - Sorularınız için bana [GitHub Profilimden](https://github.com/oguz00000) ulaşabilirsiniz.