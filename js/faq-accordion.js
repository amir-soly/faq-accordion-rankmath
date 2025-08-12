document.addEventListener('click', function(e){
    if(e.target.classList.contains('faq-question')){
        e.target.parentElement.classList.toggle('active');
    }
});
