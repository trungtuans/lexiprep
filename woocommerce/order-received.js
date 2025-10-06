if (window.location.href.includes('/checkout/order-received/')) {
  window.addEventListener('DOMContentLoaded', () => {
    const orderElement = document.querySelector('.woocommerce-order');
    if (orderElement) {
      const elementPosition = orderElement.getBoundingClientRect().top;
      const offsetPosition = elementPosition + window.pageYOffset - 100;
      
      window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
      });
    }
  });
}