const initApp = () => {
  const searchForm = document.querySelector('[data-live-filter]');
  const cards = Array.from(document.querySelectorAll('[data-listing-card]'));
  const emptyState = document.querySelector('[data-empty-state]');

  if (searchForm && cards.length) {
    const cityInput = searchForm.querySelector('[name="city"]');
    const budgetInput = searchForm.querySelector('[name="budget"]');

    const applyFilter = () => {
      const cityQuery = (cityInput?.value || '').trim().toLowerCase();
      const budgetQuery = Number.parseInt((budgetInput?.value || '').trim(), 10);
      let visibleCount = 0;

      cards.forEach((card) => {
        const city = (card.dataset.city || '').toLowerCase();
        const budget = Number.parseInt(card.dataset.budget || '0', 10);
        const matchesCity = !cityQuery || city.includes(cityQuery);
        const matchesBudget = Number.isNaN(budgetQuery) || budget <= budgetQuery;
        const shouldShow = matchesCity && matchesBudget;

        card.classList.toggle('d-none', !shouldShow);
        if (shouldShow) {
          visibleCount += 1;
        }
      });

      if (emptyState) {
        emptyState.classList.toggle('d-none', visibleCount > 0);
      }
    };

    cityInput?.addEventListener('input', applyFilter);
    budgetInput?.addEventListener('input', applyFilter);
    applyFilter();
  }

  document.querySelectorAll('[data-auto-dismiss]').forEach((alertElement) => {
    window.setTimeout(() => {
      alertElement.classList.remove('show');
      alertElement.classList.add('fade');
    }, 4500);
  });

  const navCollapse = document.getElementById('mainNav');
  if (navCollapse && window.bootstrap?.Collapse) {
    const collapseApi = new window.bootstrap.Collapse(navCollapse, { toggle: false });
    navCollapse.querySelectorAll('a.nav-link, button.nav-link').forEach((link) => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 1500) {
          collapseApi.hide();
        }
      });
    });
  }
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  initApp();
}
