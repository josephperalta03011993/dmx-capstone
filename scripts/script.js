document.addEventListener('DOMContentLoaded', () => {
    const sidebarLinks = document.querySelectorAll('.sidebar > ul > li > a');

    sidebarLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            const parentLi = link.parentElement;
            const submenu = parentLi.querySelector('ul');
            
            if (submenu) {
                event.preventDefault();
                console.log("Link clicked:", link); 
                parentLi.classList.toggle('active');
                console.log("Toggled active class on:", parentLi);    

                // close other open submenu
                sidebarLinks.forEach(otherLink => {
                    if (otherLink !== link && otherLink.parentElement.classList.contains('active')) {
                        otherLink.parentElement.classList.remove('active');
                        console.log("Removed active class from:", otherLink.parentElement);
                    }
                });
            } else {
                console.log(`Navigate to ${link.getAttribute('href')}`);
            }

        });
    });
});
