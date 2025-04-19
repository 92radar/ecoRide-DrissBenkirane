document.addEventListener("DOMContentLoaded", function () {
    const filterForm = document.querySelector("form");
    const resultsContainer = document.querySelectorAll(".publication-cadre");
    const applyFiltersBtn = document.getElementById("applyFiltersBtn");

    applyFiltersBtn.addEventListener("click", function (event) {
        event.preventDefault();

        const selectedTypes = Array.from(document.querySelectorAll("input[name='Electrique']:checked, input[name='Hybride']:checked, input[name='Essence']:checked"))
            .map(input => input.value);

        const minPrice = document.getElementById("prixmini").value;
        const maxPrice = document.getElementById("prixmaxi").value;
        const maxDuration = document.getElementById("dureeMax").value;

        const selectedRatings = Array.from(document.querySelectorAll("input[name='evaluation']:checked"))
            .map(input => parseInt(input.value));

        resultsContainer.forEach(result => {
            const carType = result.querySelector(".energie").textContent.trim();
            const price = parseFloat(result.querySelector(".prix").textContent.replace(/[^0-9.-]+/g, ""));
            const duration = parseInt(result.querySelector(".duree").textContent.trim());

            const rating = result.querySelector(".note").textContent.trim();
            const ratingValue = parseInt(rating);


            let show = true;

            if (selectedTypes.length > 0 && !selectedTypes.includes(carType)) {
                show = false;
            }

            if (minPrice && price < minPrice) {
                show = false;
            }
            if (maxPrice && price > maxPrice) {
                show = false;
            }

            if (maxDuration && duration > maxDuration) {
                show = false;
            }

            if (selectedRatings.length > 0 && !selectedRatings.includes(ratingValue)) {
                show = false;
            }

            result.style.display = show ? "block" : "none";
        });
    });
});

document.getElementById("toggleSidebar").addEventListener("click", function () {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
});