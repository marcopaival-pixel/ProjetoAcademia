/**
 * Food Lookup Autocomplete + Barcode Scanner
 * Integração com API Laravel do Open Food Facts
 */
(function() {
  'use strict';

  const CONFIG = {
    apiBaseUrl: 'http://localhost:8000',
    searchEndpoint: '/api/food/search',
    productEndpoint: '/api/food/product',
  };

  // ============================================================================
  // HELPERS
  // ============================================================================

  function escape(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => func(...args), delay);
    };
  }

  function showNotification(message, type = 'info') {
    const container = document.getElementById('food-lookup-notification');
    if (!container) return;
    
    container.innerHTML = `
      <div class="alert alert-${type}" style="margin: 0.5rem 0; padding: 0.75rem; border-radius: 0.25rem;">
        ${escape(message)}
      </div>
    `;
    setTimeout(() => {
      container.innerHTML = '';
    }, 4000);
  }

  function applyProductToForm(product) {
    const foodName = document.getElementById('food_name');
    const calories = document.getElementById('calories');
    const protein = document.getElementById('protein_g');
    const carbs = document.getElementById('carbs_g');
    const fat = document.getElementById('fat_g');

    if (!foodName) return;

    let label = product.name || '';
    if (product.brands) {
      label += ' — ' + product.brands;
    }
    if (label.length > 200) {
      label = label.slice(0, 197) + '…';
    }

    foodName.value = label;
    if (calories) calories.value = product.calories || '';
    if (protein) protein.value = product.protein_g || '';
    if (carbs) carbs.value = product.carbs_g || '';
    if (fat) fat.value = product.fat_g || '';

    showNotification(
      `Preenchido com ${product.basis || '100g'}. Revise antes de guardar.`,
      'success'
    );
  }

  async function fetchProductByCode(codeDigits) {
    try {
      const response = await fetch(
        `${CONFIG.apiBaseUrl}${CONFIG.productEndpoint}/${encodeURIComponent(codeDigits)}`,
        {
          headers: { 'Accept': 'application/json' },
        }
      );

      if (response.status === 429) {
        showNotification('Muitos pedidos. Aguarde um minuto.', 'error');
        return null;
      }

      if (!response.ok) {
        const data = await response.json().catch(() => ({}));
        showNotification(
          data.error || `Erro ${response.status}: Produto não encontrado`,
          'error'
        );
        return null;
      }

      const data = await response.json();
      if (!data.ok) {
        showNotification(data.error || 'Erro ao carregar produto.', 'error');
        return null;
      }

      return data.product || null;
    } catch (err) {
      showNotification('Erro de rede ao carregar produto.', 'error');
      console.error('Fetch error:', err);
      return null;
    }
  }

  async function fetchProductsByQuery(query, page = 1) {
    const q = (query || '').trim();
    if (q.length < 2) {
      showNotification('Digite pelo menos 2 caracteres.', 'error');
      return [];
    }

    try {
      const response = await fetch(
        `${CONFIG.apiBaseUrl}${CONFIG.searchEndpoint}?q=${encodeURIComponent(q)}`,
        {
          headers: { 'Accept': 'application/json' },
        }
      );

      if (response.status === 429) {
        showNotification('Muitos pedidos. Aguarde um minuto.', 'error');
        return [];
      }

      if (!response.ok) {
        showNotification(`Erro ${response.status} na busca.`, 'error');
        return [];
      }

      const data = await response.json();
      return (data.products && Array.isArray(data.products)) ? data.products : [];
    } catch (err) {
      showNotification('Erro de rede ao buscar produtos.', 'error');
      console.error('Fetch error:', err);
      return [];
    }
  }

  // ============================================================================
  // AUTOCOMPLETE DROPDOWN
  // ============================================================================

  function createDropdown(query) {
    const existing = document.getElementById('food-lookup-dropdown');
    if (existing) existing.remove();

    const container = document.createElement('div');
    container.id = 'food-lookup-dropdown';
    container.style.cssText = `
      position: absolute;
      background: white;
      border: 1px solid #ddd;
      border-radius: 0.25rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      z-index: 1000;
      max-height: 300px;
      overflow-y: auto;
      min-width: 200px;
    `;
    container.innerHTML = '<div style="padding: 0.75rem; color: #666; font-size: 0.875rem;">Pesquisando…</div>';

    return container;
  }

  function positionDropdown(inputEl, dropdown) {
    const rect = inputEl.getBoundingClientRect();
    dropdown.style.top = (window.scrollY + rect.bottom) + 'px';
    dropdown.style.left = (window.scrollX + rect.left) + 'px';
    dropdown.style.width = rect.width + 'px';
  }

  function showDropdownResults(products, inputEl, dropdown, onSelect) {
    if (products.length === 0) {
      dropdown.innerHTML = '<div style="padding: 0.75rem; color: #999; font-size: 0.875rem;">Nenhum resultado encontrado.</div>';
      return;
    }

    dropdown.innerHTML = products.map((p, idx) => `
      <div 
        class="food-lookup-item"
        data-index="${idx}"
        style="
          padding: 0.75rem;
          border-bottom: 1px solid #eee;
          cursor: pointer;
          transition: background 0.15s;
        "
        onmouseover="this.style.background='#f5f5f5'"
        onmouseout="this.style.background='white'"
      >
        <div style="font-weight: 500; font-size: 0.9375rem;">${escape(p.name || 'Sem nome')}</div>
        <div style="font-size: 0.8125rem; color: #666; margin-top: 0.25rem;">${escape(p.brands || '—')}</div>
      </div>
    `).join('');

    dropdown.querySelectorAll('.food-lookup-item').forEach((item) => {
      item.addEventListener('click', async () => {
        const idx = parseInt(item.dataset.index, 10);
        const product = products[idx];
        if (product) {
          const full = await fetchProductByCode(product.code);
          if (full) {
            applyProductToForm(full);
          }
        }
        dropdown.remove();
      });
    });
  }

  // ============================================================================
  // INITIALIZATION
  // ============================================================================

  function initFoodLookup() {
    const foodNameInput = document.getElementById('food_name');
    if (!foodNameInput) return;

    // Add container para notificações
    if (!document.getElementById('food-lookup-notification')) {
      const notifContainer = document.createElement('div');
      notifContainer.id = 'food-lookup-notification';
      foodNameInput.parentNode.insertBefore(notifContainer, foodNameInput.nextSibling);
    }

    // Wrapper para posicionar dropdown absolutamente
    const wrapper = document.createElement('div');
    wrapper.style.position = 'relative';
    wrapper.style.display = 'inline-block';
    wrapper.style.width = '100%';
    foodNameInput.parentNode.insertBefore(wrapper, foodNameInput);
    wrapper.appendChild(foodNameInput);

    // Autocomplete com debounce
    const debouncedSearch = debounce(async (query) => {
      if (query.length < 2) {
        const dd = document.getElementById('food-lookup-dropdown');
        if (dd) dd.remove();
        return;
      }

      let dropdown = document.getElementById('food-lookup-dropdown');
      if (!dropdown) {
        dropdown = createDropdown(query);
        document.body.appendChild(dropdown);
      }

      positionDropdown(foodNameInput, dropdown);

      const products = await fetchProductsByQuery(query);
      showDropdownResults(products, foodNameInput, dropdown, (product) => {
        applyProductToForm(product);
      });
    }, 300);

    foodNameInput.addEventListener('input', (evt) => {
      debouncedSearch(evt.target.value);
    });

    // Fechar dropdown ao clicar fora
    document.addEventListener('click', (evt) => {
      if (evt.target === foodNameInput) return;
      const dd = document.getElementById('food-lookup-dropdown');
      if (dd) dd.remove();
    });
  }

  function initBarcodeScanner() {
    const barcodeInput = document.getElementById('food_barcode');
    const scanBtn = document.getElementById('food_barcode_btn');

    if (!scanBtn || !barcodeInput) return;

    scanBtn.addEventListener('click', async () => {
      const raw = (barcodeInput.value || '').trim();
      const digits = raw.replace(/\D/g, '');

      if (digits.length < 8) {
        showNotification('Indique um código de barras válido (mín. 8 dígitos).', 'error');
        return;
      }

      showNotification('Carregando produto…', 'info');
      const product = await fetchProductByCode(digits);
      if (product) {
        applyProductToForm(product);
        barcodeInput.value = '';
      }
    });

    // Enter para escanear
    barcodeInput.addEventListener('keypress', (evt) => {
      if (evt.key === 'Enter') {
        evt.preventDefault();
        scanBtn.click();
      }
    });
  }

  // Inicializar quando DOM estiver pronto
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        initFoodLookup();
        initBarcodeScanner();
      }, 100);
    });
  } else {
    setTimeout(() => {
      initFoodLookup();
      initBarcodeScanner();
    }, 100);
  }
})();
