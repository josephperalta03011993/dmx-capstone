document.addEventListener('DOMContentLoaded', () => {
    const sidebarLinks = document.querySelectorAll('.sidebar > ul > li > a');

    sidebarLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            console.log("Link clicked:", link);

            const parentLi = link.parentElement;
            parentLi.classList.toggle('active');
            console.log("Toggled active class on:", parentLi);

            sidebarLinks.forEach(otherLink => {
                if (otherLink !== link && otherLink.parentElement.classList.contains('active')) {
                    otherLink.parentElement.classList.remove('active');
                    console.log("Removed active class from:", otherLink.parentElement);
                }
            });
        });
    });
});
