# Portfolio Développeur Fullstack

Un portfolio professionnel moderne et responsive pour développeur fullstack, créé avec HTML5, CSS3, JavaScript, Bootstrap et PHP.

## 🚀 Fonctionnalités

- **Design moderne et responsive** avec Bootstrap 5
- **Animations fluides** et interactions utilisateur
- **Navigation smooth scroll** entre les sections
- **Formulaire de contact fonctionnel** avec validation PHP
- **Sections complètes** : Accueil, Compétences, Projets, Expériences, Contact
- **Palette de couleurs professionnelle** (bleu #1E90FF, gris clair #F5F7FA)
- **Typographie moderne** avec la police Inter
- **Optimisé pour tous les appareils** (desktop, tablette, mobile)

## 📁 Structure du projet

```
portefeuille/
├── index.html              # Page principale
├── contact.php             # Traitement du formulaire de contact
├── README.md               # Documentation
└── assets/
    ├── css/
    │   └── style.css       # Styles personnalisés
    ├── js/
    │   └── script.js       # Animations et interactions
    └── images/             # Images du portfolio
        ├── profile.jpg     # Photo professionnelle
        ├── project1.jpg    # Capture projet 1
        ├── project2.jpg    # Capture projet 2
        ├── project3.jpg    # Capture projet 3
        ├── project4.jpg    # Capture projet 4
        ├── project5.jpg    # Capture projet 5
        └── project6.jpg    # Capture projet 6
```

## 🛠️ Technologies utilisées

- **HTML5** - Structure sémantique
- **CSS3** - Styles et animations
- **JavaScript ES6+** - Interactions et animations
- **Bootstrap 5** - Framework responsive
- **PHP** - Traitement du formulaire de contact
- **Font Awesome** - Icônes
- **Google Fonts** - Typographie (Inter)

## 🎨 Design

### Palette de couleurs
- **Primaire** : #1E90FF (Bleu)
- **Secondaire** : #F5F7FA (Gris clair)
- **Texte sombre** : #2c3e50
- **Texte clair** : #6c757d
- **Blanc** : #ffffff

### Typographie
- **Police principale** : Inter (Google Fonts)
- **Poids** : 300, 400, 500, 600, 700

## 📱 Responsive Design

Le portfolio s'adapte parfaitement à tous les écrans :
- **Desktop** : 1200px et plus
- **Tablette** : 768px - 1199px
- **Mobile** : 320px - 767px

## ⚡ Animations

- **Scroll animations** avec Intersection Observer
- **Effets de hover** sur les cartes et boutons
- **Animations de frappe** pour le titre principal
- **Particules flottantes** dans la section hero
- **Transitions fluides** entre les sections

## 📧 Configuration du formulaire de contact

1. Modifiez l'email de destination dans `contact.php` :
```php
$to_email = "votre-email@exemple.com";
```

2. Assurez-vous que votre serveur supporte la fonction `mail()` de PHP

3. Pour un environnement de production, configurez un service d'email SMTP

## 🚀 Installation

1. **Cloner ou télécharger** le projet
2. **Placer les fichiers** dans votre serveur web (Apache/Nginx)
3. **Configurer PHP** si nécessaire
4. **Ajouter vos images** dans le dossier `assets/images/`
5. **Personnaliser** les informations dans `index.html`
6. **Configurer l'email** dans `contact.php`

## 📝 Personnalisation

### Informations personnelles
Modifiez dans `index.html` :
- Nom et titre dans la section hero
- Description personnelle
- Compétences et niveaux
- Projets et descriptions
- Expériences professionnelles
- Informations de contact

### Images
Remplacez les images dans `assets/images/` :
- `profile.jpg` : Votre photo professionnelle
- `project1.jpg` à `project6.jpg` : Captures de vos projets

### Couleurs
Modifiez les variables CSS dans `assets/css/style.css` :
```css
:root {
    --primary-color: #1E90FF;
    --secondary-color: #F5F7FA;
    /* ... autres variables */
}
```

## 🔧 Optimisations

- **Images optimisées** pour le web
- **CSS minifié** en production
- **JavaScript optimisé** avec gestion d'erreurs
- **SEO friendly** avec balises meta appropriées
- **Performance** avec lazy loading des images

## 📞 Support

Pour toute question ou personnalisation, n'hésitez pas à me contacter !

## 📄 Licence

Ce projet est personnel et toute utilisation pour vos besoins personnels et professionnels requiert mon avale.

---

**Développé avec ❤️ par Djorwe Boulga**
