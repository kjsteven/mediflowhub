// HEADER 

// Get the header element
const header = document.querySelector('.site-header');
    
// Add an event listener to the window's scroll event
window.addEventListener('scroll', () => {
    // Check if the scroll position is greater than a certain value (e.g., 100px)
    if (window.scrollY > 100) {
        // Add the class to change the background color
        header.classList.add('scrolled');
    } else {
        // Remove the class to revert the background color
        header.classList.remove('scrolled');
    }
});

