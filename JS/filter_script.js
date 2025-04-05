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
            const carType = result.querySelector(".energie").textContent.split(': ')[1];
            const price = parseFloat(result.querySelector(".prix").previousSibling.nodeValue);
            const duration = result.querySelector(".dates").textContent;
            const rating = result.dataset.rating ? parseInt(result.dataset.rating) : 0;

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

            if (selectedRatings.length > 0 && !selectedRatings.includes(rating)) {
                show = false;
            }

            result.style.display = show ? "block" : "none";
        });
    });
});