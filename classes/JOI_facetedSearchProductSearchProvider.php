<?php

//Use pour la recherche standard
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;

//Use pour les facettes
use PrestaShop\PrestaShop\Core\Product\Search\FacetCollection; #Collection de facettes
use PrestaShop\PrestaShop\Core\Product\Search\Facet; #Classe de la facette
use PrestaShop\PrestaShop\Core\Product\Search\Filter; #Classe des filtres
use PrestaShop\PrestaShop\Core\Product\Search\URLFragmentSerializer; #Pour transformer l'url

//Provider par défaut
use PrestaShop\PrestaShop\Adapter\NewProducts\NewProductsProductSearchProvider;

class JOI_facetedSearchProductSearchProvider implements ProductSearchProviderInterface {
    private $module;
    private $sortOrderFactory;

    /*
     * Instanciation de la classe
     * @param JumpOnIt $module
     */
    public function __construct( JumpOnIt $module) {
        $this->module = $module;

        //Récupération des tris disponibles par défaut
        $this->sortOrderFactory = new SortOrderFactory($this->module->getTranslator());
    }

    public function runQuery(ProductSearchContext $context, ProductSearchQuery $query)
    {
        if (!$products = $this->getProductsOrCount($context, $query, 'count')) {
            $products = array();
        }

        $count = $this->getProductsOrCount($context, $query, 'count');

        /**
         * Gestion du résultat
         * Envoi de la productSearcResult
         */

        $results = new ProductSearchResult();

        if (!empty($products)) {
            // Définition des résultats des produits
            $results
                ->setTotalProductsCount($count)
                ->setProducts($products);
            // Définition des tris disponibles ( utilisation de ceux par défaut )
            $results->setAvailableSortOrders($this->sortOrderFactory->getDefaultSortOrders());

            // Récupération des filtres actifs
            $activeFilters = explode('|', $query->getEncodedFacets());

            // Définition des facettes disponibles
            $results->setFacetCollection($this->getSampleFacets($activeFilters));

            // Définition des facettes actuellement utilisées
            $results->setEncodedFacets($query->getEncodedFacets());
        }

        return $results;
    }

    public function getProductsOrCount(ProductSearchContext $context, ProductSearchQuery $query, $type = 'products') {
        return Product::getNewProducts(
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            $type !== 'products',
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay()
        );
    }

    /**
     * Fonction d'explication sur comment afficher des facettes
     * @return FacetCollection
     */

    protected function getSampleFacets($activeFilters) {
        // Gestion des filtres actifs
        $activeFiltersQueryString = '';
        $activeFiltersQueryString .= implode('|', $activeFilters);

        //Création d'une collection de facettes
        $collection = new FacetteCollection();

        // Création d'une facette
        $facet = new Facet();
        $facet->setLabel('Localisation')
            ->setType('custom')
            ->setDisplayed(true)
            ->setWidgetType('checkbox')
            ->setMultipleSelectionAllowed(true);

        // TODO : getSellersPositions()

        // Ajout de filtre a cette facette
        $encodedFacetsUrl1 = $activeFiltersQueryString != '' ? $activeFiltersQueryString."|cp1": "cp1";

        $filter1 = new Filter();
        $filter1->setLabel('Eiguilles')
            ->setDisplayed(true)
            ->setActive(in_array('cp1') ? true : false)
            ->setType('number')
            ->setValue('84120')
            ->setNextEncodedFacets($encodedFacetsUrl1)
            ->setMagnitude(1);

        // Ajout du filtre à la facette
        $facet->addFilter($filter1);

        // Ajout d'un 2e filtre a cette facette
        $encodedFacetsUrl2 = $activeFiltersQueryString != '' ? $activeFiltersQueryString."|cp2": "cp2";

        $filter2 = new Filter();
        $filter2->setLabel('Mallemort')
            ->setDisplayed(true)
            ->setActive(in_array('cp2') ? true : false)
            ->setType('number')
            ->setValue('13370')
            ->setNextEncodedFacets($encodedFacetsUrl2)
            ->setMagnitude(3);

        // Ajout du filtre à la facette
        $facet->addFilter($filter2);

        // Ajout de la facette à la collection
        $collection->addFacet($facet);

        // Renvoi de la collection de facette
        return $collection;
    }
}