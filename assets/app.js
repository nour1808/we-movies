import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

import './styles/app.scss';
import 'bootstrap';
import Swal from 'sweetalert2';

// Fonction pour afficher les détails du film dans une popup
function showMovieDetails(movieId) {
    fetch(`/movies/${movieId}/details`)
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: data.videoMovie.name,
                html: `
                    <div class="embed-responsive embed-responsive-16by9 mb-3">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/${data.videoMovie.key}" allowfullscreen></iframe>
                    </div>
                    <p>Film : ${data.detailsMovie.tagline}  <span class="badge bg-dark">${data.detailsMovie.vote_average}</span> pour  ${data.detailsMovie.vote_count} utilisateurs</p>
                `,
                showCloseButton: false,
                focusConfirm: false,
                confirmButtonText: 'Fermer',
                width: '60%',
                padding: '1em'
            });
        })
        .catch(error => console.error('Erreur lors du chargement des détails du film:', error));
}

// Fonction pour rediriger avec le genre sélectionné
function redirectWithGenre(genreId) {
    const baseUrl = window.location.origin; 
    const urlParams = new URLSearchParams(window.location.search);

    urlParams.set('with_genres', genreId);

    window.location.href = `${baseUrl}/?${urlParams.toString()}`;
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-details').forEach(button => {
        button.addEventListener('click', (event) => {
            const movieId = event.target.getAttribute('data-movie-id');
            showMovieDetails(movieId);
        });
    });

    document.querySelectorAll('.form-check-input').forEach(input => {
        input.addEventListener('change', (event) => {
            if (event.target.checked) {
                redirectWithGenre(event.target.value);
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');

    // Initialiser Awesomplete
    /*new Awesomplete(searchInput, {
        minChars: 2,
        autoFirst: true,
        fetch: function(text, update) {
            fetch(`/movies?search=${text}`)
                .then(response => response.json())
                .then(data => {
                    // Mappez les résultats pour Awesomplete
                    update(data.movies);
                });
        },
        item: function(item) {
            return Awesomplete.$('<div>' + item + '</div>');
        }
    });*/

    // Gérer la soumission du formulaire
    searchForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const query = document.getElementById('searchInput').value.trim();
        if (query) {
            const baseUrl = window.location.origin;
            window.location.href = `${baseUrl}/?search=${encodeURIComponent(query)}`;
        }
    });

});





