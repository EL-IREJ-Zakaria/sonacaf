document.addEventListener('DOMContentLoaded', function() {
    // Menu hamburger
    const hamburger = document.querySelector('.hamburger');
    const menu = document.querySelector('.menu');
    
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            menu.classList.toggle('active');
        });
    }
    
    // Fermer le menu en cliquant sur un lien
    const menuLinks = document.querySelectorAll('.menu a');
    
    menuLinks.forEach(link => {
        link.addEventListener('click', function() {
            hamburger.classList.remove('active');
            menu.classList.remove('active');
        });
    });
    
    // Animation au scroll
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.service-card, .why-us-card, .testimonial');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (elementPosition < windowHeight - 100) {
                element.classList.add('animate');
            }
        });
    };
    
    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll(); // Exécuter une fois au chargement
    
    // Gestion du bouton de soumission en fonction de la case RGPD
    const rgpdCheckbox = document.getElementById('rgpd');
    const submitButton = document.querySelector('.btn-primary');
    
    // Désactiver le bouton au chargement de la page
    if (submitButton && rgpdCheckbox) {
        submitButton.disabled = !rgpdCheckbox.checked;
        
        // Ajouter un écouteur d'événement pour la case RGPD
        rgpdCheckbox.addEventListener('change', function() {
            submitButton.disabled = !this.checked;
        });
    }
    
    // Validation du formulaire
    const contactForm = document.getElementById('contact-form');
    const formMessage = document.getElementById('form-message');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validation côté client
            if (validateForm()) {
                // Envoi du formulaire via AJAX
                submitForm();
            }
        });
    }
    
    function validateForm() {
        let isValid = true;
        const nom = document.getElementById('nom');
        const prenom = document.getElementById('prenom');
        const anneeNaissance = document.getElementById('annee_naissance');
        const codePostal = document.getElementById('code_postal');
        const telephone = document.getElementById('telephone');
        const email = document.getElementById('email');
        const service = document.getElementById('service');
        const message = document.getElementById('message');
        const rgpd = document.getElementById('rgpd');
        
        // Réinitialiser les messages d'erreur
        resetErrors();
        
        // Validation du nom
        if (nom.value.trim() === '') {
            showError(nom, 'Le nom est requis');
            isValid = false;
        }
        
        // Validation du prénom
        if (prenom.value.trim() === '') {
            showError(prenom, 'Le prénom est requis');
            isValid = false;
        }
        
        // Validation de l'année de naissance
        const currentYear = new Date().getFullYear();
        if (anneeNaissance.value.trim() === '' || isNaN(anneeNaissance.value) || 
            anneeNaissance.value < 1900 || anneeNaissance.value > (currentYear - 18)) {
            showError(anneeNaissance, `Veuillez entrer une année de naissance valide (entre 1900 et ${currentYear - 18})`);
            isValid = false;
        }
        
        // Validation du code postal
        if (codePostal.value.trim() === '' || !/^\d{5}$/.test(codePostal.value)) {
            showError(codePostal, 'Veuillez entrer un code postal valide (5 chiffres)');
            isValid = false;
        }
        
        // Validation du téléphone
        if (telephone.value.trim() === '' || !/^(0|\+33)[1-9]([-. ]?\d{2}){4}$/.test(telephone.value)) {
            showError(telephone, 'Veuillez entrer un numéro de téléphone valide');
            isValid = false;
        }
        
        // Validation de l'email
        if (email.value.trim() === '' || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            showError(email, 'Veuillez entrer une adresse email valide');
            isValid = false;
        }
        
        // Validation du service
        if (service.value === '') {
            showError(service, 'Veuillez sélectionner un objet pour votre demande');
            isValid = false;
        }
        
        // Validation du message
        if (message.value.trim() === '') {
            showError(message, 'Veuillez entrer un message');
            isValid = false;
        }
        
        // Validation RGPD
        if (!rgpd.checked) {
            showError(rgpd, 'Vous devez accepter la politique de confidentialité');
            isValid = false;
        }
        
        return isValid;
    }
    
    function showError(input, message) {
        const formGroup = input.parentElement;
        const errorElement = document.createElement('div');
        errorElement.className = 'error-text';
        errorElement.textContent = message;
        errorElement.style.color = '#b91c1c';
        errorElement.style.fontSize = '0.8rem';
        errorElement.style.marginTop = '5px';
        formGroup.appendChild(errorElement);
        input.style.borderColor = '#b91c1c';
    }
    
    function resetErrors() {
        const errorTexts = document.querySelectorAll('.error-text');
        errorTexts.forEach(error => error.remove());
        
        const inputs = contactForm.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.style.borderColor = '#ddd';
        });
    }
    
    function submitForm() {
        const formData = new FormData(contactForm);
        
        // Afficher un indicateur de chargement
        formMessage.innerHTML = '<div class="loading">Envoi en cours...</div>';
        formMessage.style.display = 'block';
        
        fetch('process_form.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                formMessage.innerHTML = `<div class="success-message">${data.message}</div>`;
                contactForm.reset();
            } else {
                formMessage.innerHTML = `<div class="error-message">${data.message}</div>`;
            }
        })
        .catch(error => {
            formMessage.innerHTML = '<div class="error-message">Une erreur est survenue lors de l\'envoi du formulaire. Veuillez réessayer.</div>';
            console.error('Erreur:', error);
        });
    }
});