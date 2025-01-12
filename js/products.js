$(function () {
  new ProductTableApp($('#product-table-app'), '../php/fetchProducts.php');
});

// ----------------------------------------
// Class Definition

class ProductTableApp {
  /**
   * @param {JQuery<HTMLElement>} $el
   * @param {string} url
   */
  constructor($el, url) {
    this.$el = $el;
    this.initState();
    this.defineBaseElements();
    this.renderLoading();

    this.fetchData(url)
      .then((products) => this.handleFetchSuccess(products))
      .catch((err) => this.handleFetchError(err));
  }

  /* -----------------------------
   *    Initialization
   * -----------------------------
   */
  initState() {
      const itemHeight = 75;
      const viewportHeight = window.innerHeight;
      const itemsPerPage = Math.floor(viewportHeight / itemHeight);
      console.log(itemsPerPage);
      console.log(viewportHeight);

      this.state = {
          isLoaded: false,
          products: [],
          error: null,
          pagination: {
              currentPage: 1,
              itemsPerPage: itemsPerPage,
          },
      };
  }

  /**
   * For elements that exist regardless of data
   */
  defineBaseElements() {
    this.$tbody = this.$el.find('tbody');
    this.$noResults = this.$el.find('#no-results');
    this.$handleTable = this.$el.find('.js-handle-table');
    this.$sortBy = this.$el.find('#sort-by');
    this.$filterBrand = this.$el.find('#filter-brand');
    this.$filterCategory = this.$el.find('#filter-category');
    this.$hidingOutOfStock = this.$el.find('[value="hiding-out-of-stock"]');

		this.$prevPageBtn = this.$el.find('.js-prev-page');
    this.$nextPageBtn = this.$el.find('.js-next-page');
    this.$currentPage = this.$el.find('.js-current-page');
    this.$totalPages = this.$el.find('.js-total-pages');
    this.$pageInput = this.$el.find('.js-page-input');
    this.$goPageBtn = this.$el.find('.js-go-page');
  }

  /* -----------------------------
   *    Data Fetch / Handlers
   * -----------------------------
   */
  /**
   * @param {string} url
   * @returns {Promise<Object[]>}
   */
  fetchData(url) {
    return $.ajax({
      url,
      dataType: 'json',
    });
  }

  handleFetchSuccess(products) {
    this.state.isLoaded = true;
    this.state.products = products;

    this.populateFilterOptions();

    this.render(products);

    this.bindEvents();
  }

  handleFetchError(err) {
    this.state.error = err;
    console.error(`Fetch error: ${err.responseText || err}`);
  }

  /* -----------------------------
   *    Events / Listeners
   * -----------------------------
   */
  bindEvents() {
    this.handleTableChange = this.handleTableChange.bind(this);
    this.handlePrevPage = this.handlePrevPage.bind(this);
    this.handleNextPage = this.handleNextPage.bind(this);
		this.handleGoPage = this.handleGoPage.bind(this);

    this.$handleTable.on('change', this.handleTableChange);

    this.$prevPageBtn.on('click', this.handlePrevPage);
    this.$nextPageBtn.on('click', this.handleNextPage);
		this.$goPageBtn.on('click', this.handleGoPage);
  }

  handleTableChange() {
    this.render();
  }

  handlePrevPage() {
    if (this.state.pagination.currentPage > 1) {
      this.state.pagination.currentPage -= 1;
      this.render();
    }
  }

  handleNextPage() {
    const filteredProducts = this.getFilteredSortedProducts();
    const totalPages = Math.ceil(
      filteredProducts.length / this.state.pagination.itemsPerPage
    );

    if (this.state.pagination.currentPage < totalPages) {
      this.state.pagination.currentPage += 1;
      this.render();
    }
  }

	handleGoPage() {
		const inputVal = parseInt(this.$pageInput.val(), 10);

		if (isNaN(inputVal)) {
			return;
		}

		const { itemsPerPage } = this.state.pagination;
		const filteredProducts = this.getFilteredSortedProducts();
		const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);

		if (inputVal < 1) {
			this.state.pagination.currentPage = 1;
		} else if (inputVal > totalPages) {
			this.state.pagination.currentPage = totalPages;
		} else {
			this.state.pagination.currentPage = inputVal;
		}

		this.$pageInput.val('');

		this.render();
	}

  /* -----------------------------
   *    UI Population
   * -----------------------------
   */
  populateFilterOptions() {
    const { products } = this.state;
    const brands = [...new Set(products.map((p) => p.brand))];
    const categories = [...new Set(products.map((p) => p.category))];

    this.$filterBrand.empty().append('<option value="all">All</option>');
    brands.forEach((brand) => {
      this.$filterBrand.append(`<option value="${brand}">${brand}</option>`);
    });

    this.$filterCategory.empty().append('<option value="all">All</option>');
    categories.forEach((category) => {
      this.$filterCategory.append(
        `<option value="${category}">${category}</option>`
      );
    });
  }

  /* -----------------------------
   *    Helper: Filter + Sort
   * -----------------------------
   */
  getFilteredSortedProducts() {
    const sorted = this.sortProducts(this.state.products);
    const filtered = this.filterProducts(sorted);
    const toggled = this.toggleStocked(filtered);
    return toggled;
  }

  /**
   * @param {Object[]} products
   * @returns {Object[]}
   */
  sortProducts(products) {
    const sortKey = this.$sortBy.val();

    switch (sortKey) {
      case 'none':
        return products;

      case 'price':
        return [...products].sort((a, b) => a.price - b.price);

      case 'created_at':
      case 'updated_at':
        return [...products].sort((a, b) => {
          const dateA = this.parseDate(a[sortKey]);
          const dateB = this.parseDate(b[sortKey]);

          if (isNaN(dateA) || isNaN(dateB)) {
            console.error(`Invalid date format`, { a, b });
            return 0;
          }
          return dateA - dateB;
        });

      default:
        return products;
    }
  }

  parseDate(dateString) {
    return new Date(dateString.replace(/\//g, '-'));
  }

  /**
   * @param {Object[]} products
   * @returns {Object[]}
   */
  filterProducts(products) {
    const selectedBrand = this.$filterBrand.val();
    const selectedCategory = this.$filterCategory.val();

    return products
      .filter((product) => {
        if (selectedBrand === 'all') return true;
        return product.brand === selectedBrand;
      })
      .filter((product) => {
        if (selectedCategory === 'all') return true;
        return product.category === selectedCategory;
      });
  }

  /**
   * Toggle visibility of out-of-stock products
   * @param {Object[]} products
   * @returns {Object[]}
   */
  toggleStocked(products) {
    return this.$hidingOutOfStock.prop('checked')
      ? products.filter((product) => product.stocked === true)
      : products;
  }

  /* -----------------------------
   *    Rendering
   * -----------------------------
   */
  render(productsArg) {
    const productsToRender =
      productsArg || this.getFilteredSortedProducts() || [];

    if (!this.state.isLoaded) {
      this.renderLoading();
      return;
    }

    const paginated = this.getPaginatedProducts(productsToRender);

    if (paginated.length === 0 && productsToRender.length > 0) {
      this.state.pagination.currentPage = 1;
      return this.render();
    }

    this.renderTableBody(paginated);

    if (productsToRender.length === 0) {
      this.$noResults.removeClass('hidden');
    } else {
      this.$noResults.addClass('hidden');
    }

    this.updatePaginationControls(productsToRender.length);
  }

  /**
   * Slices out the products for the current page
   * @param {Object[]} products
   * @returns {Object[]}
   */
  getPaginatedProducts(products) {
    const { currentPage, itemsPerPage } = this.state.pagination;
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    return products.slice(startIndex, endIndex);
  }

  /**
   * Updates the Next/Prev button states and page display
   * @param {number} totalItems
   */
  updatePaginationControls(totalItems) {
    const { currentPage, itemsPerPage } = this.state.pagination;
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    this.$currentPage.text(currentPage);
    this.$totalPages.text(totalPages);

    if (currentPage === 1) {
      this.$prevPageBtn.prop('disabled', true);
    } else {
      this.$prevPageBtn.prop('disabled', false);
    }

    if (currentPage >= totalPages) {
      this.$nextPageBtn.prop('disabled', true);
    } else {
      this.$nextPageBtn.prop('disabled', false);
    }
  }

  /**
   * Renders the table body based on the provided page of products
   * @param {Object[]} products
   */
  renderTableBody(products) {
    const rowsHtml = products.map((p) => this.createRow(p)).join('');
    this.$tbody.html(rowsHtml);
  }

  /**
   * Shows a error message if the data is not loaded or an error occurs during the fetch
   */
  renderLoading() {
    this.$tbody.html('<tr><td colspan="8">You have to disable debug mode to use the products table</td></tr>');
  }

  /**
   * Creates table row HTML
   * @param {Object} product
   * @returns {string}
   */
  createRow(product) {
    const twoSpaces = '&nbsp;&nbsp;';
    const stockIcon = product.stocked
      ? `<i class="fas fa-check-circle light-text"></i>${twoSpaces}In stock`
      : `<i class="fas fa-minus-circle light-text"></i>${twoSpaces}Out of stock`;

    return `
      <tr class="table-row" data-key="${product.id}">
        <td class="table-cell align-right">${product.id}</td>
        <td class="table-cell align-left">${product.brand}</td>
        <td class="table-cell align-left">${product.name}</td>
        <td class="table-cell align-left">${product.category}</td>
        <td class="table-cell align-right">&pound; ${product.price}</td>
        <td class="table-cell align-left">${stockIcon}</td>
        <td class="table-cell align-left">${product.stock}</td>
        <td class="table-cell align-left">${product.created_at}</td>
        <td class="table-cell align-left">${product.updated_at}</td>
      </tr>
    `;
  }
}
