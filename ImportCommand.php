<?php

/**
 * Class ImportCommand
 */
class ImportCommand extends CConsoleCommand {

    const GROUP_TAG_NAME = 'Группа';
    const ID_TAG_NAME = 'Ид';
    const NAME_TAG_NAME = 'Наименование';
    const GROUPS_TAG_NAME = 'Группы';
    const ART_TAG_NAME = 'Артикул';
    const COLOUR_PROPERTY_ID = '79850e95-6a9a-11e6-bca1-0026551b52ab';
    const COLOUR_BED_PROPERTY_ID = 'b670721f-3632-11e7-98ca-0026551b52ab';
    const COLOUR_PROPERTY_NAME = 'colours';
    const SITENAME_PROPERTY_ID = '6b1212d5-3631-11e7-98ca-0026551b52ab';
    const SITENAME_PROPERTY_NAME = 'sitename';
    const PROPERTY_ID_TAG_NAME = 'ИдЗначения';
    const PROPERTY_VALUE_TAG_NAME = 'Значение';
    const DESCRIPTION_TAG_NAME = 'Описание';
    const PRODUCT_TAG_NAME = 'Товар';
    const PROPERTIES_VALUES = 'ЗначенияСвойств';
    const PROPERTY_VALUES = 'ЗначенияСвойства';
    const CATEGORIES_INFILE_NAME = 'categories';
    const NORMALIZED_INFILE_NAME = 'normalized';
    const PRODUCTS_INFILE_NAME = 'products';
    const COLOURS_INFILE_NAME = 'colours';
    const STORAGES_INFILE_NAME = 'storages';
    const OFFERS_INFILE_NAME = 'offers';
    const WEIGHT_PROPERTY_NAME = 'weight';
    const PRODUCTS_TO_STORAGES_INFILE_NAME = 'products_to_storages';
    const OFFER_TAG_NAME = 'Предложение';
    const PRICES_TAG_NAME = 'Цены';
    const PRICE_TAG_NAME = 'Цена';
    const PRICE_UNIT_TAG_NAME = 'ЦенаЗаЕдиницу';
    const QUANTITY_TAG_NAME = 'Количество';
    const STORAGE_TAG_NAME = 'Склад';
    const STORAGE_ID = 'ИдСклада';
    const STORAGE_QUANTITY = 'КоличествоНаСкладе';
    const WHEELS_PROPERTY_ID = 'ef744fb6-ce81-11e6-82d0-902b346c0819';
    const BOX_PROPERTY_ID = 'ef744fb0-ce81-11e6-82d0-902b346c0819';
    const POCKET_PROPERTY_ID = '07071f56-39f4-11e6-a2fd-0026551b52ab';
    const THROWN_HANDLE_PROPERTY_ID = '9cf092a6-39f2-11e6-a2fd-0026551b52ab';
    const SETTLED_HANDLE_PROPERTY_ID = 'c39cc7c6-39f2-11e6-a2fd-0026551b52ab';
    const VIEWED_WINDOW_PROPERTY_ID = 'f2be1946-39f3-11e6-a2fd-0026551b52ab';
    const SUN_SECURITY_PEAK_WINDOW_PROPERTY_ID = '1654a9f6-39f4-11e6-a2fd-0026551b52ab';
    const LIGHT_REFLECTING_ELEMENT_PROPERTY_ID = '28f27107-39f4-11e6-a2fd-0026551b52ab';
    const NICK_CUSHION_BUCKET_PROPERTY_ID = '35153376-39f2-11e6-a2fd-0026551b52ab';
    const WEIGHT_PROPERTY_ID = 'c2bd4a2c-cce4-11e6-a211-902b346c0819';
    const CHECKBOX_ATTRIBUTE_GROUP_TYPE = '0';
    const CATALOG_ATTRIBUTE_GROUP_TYPE = '1';
    const NUMBER_ATTRIBUTE_GROUP_TYPE = '2';
    const OPTIONS_ATTRIBUTE_GROUP_TYPE = '3';
    const VALUES_VARIANTS_TAG_NAME = 'ВариантыЗначений';
    const CATALOG_TAG_NAME = 'Справочник';
    const CATALOG_ATTRIBUTES_INFILE_NAME = 'catalog_attributes';
    const OPTIONS_ATTRIBUTES_INFILE_NAME = 'options_attributes';
    const CATALOG_ATTRIBUTES_TO_PRODUCTS_INFILE_NAME = 'catalog_attributes_to_products';
    const PROPERTY_TAG_NAME = 'Свойство';
    const OPTIONS_ATTRIBUTE_GROUP_NAME = 'Опции';

    /** @var XMLReader */
    protected $parser = null;
    /** @var int */
    protected $level = 0;
    /** @var array|null */
    protected $groups = null;
    /** @var array */
    protected $eventVars = [];

    /** @var string */
    protected $dbfolder = '';

    /** @var array */
    protected $events = [
        'Группы' => 'onGroups',
        'Товары' => 'onProducts',
        'ЗначенияСвойств' => 'onPropertiesValues',
        'Предложения' => 'onOffers',
        'Цены' => 'onPrices',
        'Склады' => 'onStorages',
        'Свойства' => 'onPropertyDescription'
    ];

    /** @var string|null  */
    protected $currentEventKey = null;

    /** @var array|null  */
    protected $colours = null;

    /** @var string|null  */
    protected $sitename = null;

    /** @var bool */
    protected $isEventStopped = false;

    /** @var array|null  */
    protected $products = null;

    /** @var array|null  */
    protected $offers = null;

    /** @var array|null  */
    protected $storages = null;

    /** @var array | null  */
    protected $currentProduct = null;

    /** @var array | null  */
    protected $currentOffer = null;

    /** @var array | null  */
    protected $currentStorage = null;

    /** @var array */
    protected $catalogAttributes = null;

    /** @var array */
    protected $optionsAttributes = null;

    /**
     * @var mysqli | null
     */
    protected $mysql;

    protected $isFake = false;

    protected $activeCategories = [
      '9a6924d0-dd7c-11e6-8168-0026551b52ab',
      'c52b0fff-dd7c-11e6-8168-0026551b52ab',
      'd272c165-dd7c-11e6-8168-0026551b52ab',
      'ef451e23-dd7c-11e6-8168-0026551b52ab',
      '1bb400fe-dd7d-11e6-8168-0026551b52ab',
      '252b5ff4-dd7e-11e6-8168-0026551b52ab',
      'e608a6af-f802-11e6-b31d-0026551b52ab',
      '150f5b08-de30-11e6-8168-0026551b52ab',
      '5243e737-de30-11e6-8168-0026551b52ab',
      'c57bbcc2-de31-11e6-8168-0026551b52ab',
      '4ac58cf3-de33-11e6-8168-0026551b52ab',
      '9358b424-de30-11e6-8168-0026551b52ab',
      '9a590b3d-de30-11e6-8168-0026551b52ab',
      'a191442b-de30-11e6-8168-0026551b52ab',
      'a191442c-de30-11e6-8168-0026551b52ab',
      '77018e42-de33-11e6-8168-0026551b52ab',
      'a5298112-de33-11e6-8168-0026551b52ab',
      '9104976f-ff67-11e6-b31d-0026551b52ab',
      '9ac7c5f8-ff67-11e6-b31d-0026551b52ab',
      'a3092ca1-ff67-11e6-b31d-0026551b52ab',
      'c6224b90-ff67-11e6-b31d-0026551b52ab',
      'f85d1827-ff67-11e6-b31d-0026551b52ab',
      'cc7afbea-1dc7-11e7-bd7b-0026551b52ab',
      'ab3cae21-f745-11e6-b31d-0026551b52ab',
      'b412a4db-08b0-11e7-b31d-0026551b52ab',
      'dcd4201b-1dc7-11e7-bd7b-0026551b52ab',
      'f3e6533a-1dc7-11e7-bd7b-0026551b52ab'
    ];

    protected $activeCategoriesTree = [
        '9a6924d0-dd7c-11e6-8168-0026551b52ab' => '9a6924d0-dd7c-11e6-8168-0026551b52ab',
        'c52b0fff-dd7c-11e6-8168-0026551b52ab' => '9a6924d0-dd7c-11e6-8168-0026551b52ab',
        'd272c165-dd7c-11e6-8168-0026551b52ab' => '9a6924d0-dd7c-11e6-8168-0026551b52ab',
        'ef451e23-dd7c-11e6-8168-0026551b52ab' => '9a6924d0-dd7c-11e6-8168-0026551b52ab',
        '1bb400fe-dd7d-11e6-8168-0026551b52ab' => '9a6924d0-dd7c-11e6-8168-0026551b52ab',
        '252b5ff4-dd7e-11e6-8168-0026551b52ab' => '9a6924d0-dd7c-11e6-8168-0026551b52ab',
        'e608a6af-f802-11e6-b31d-0026551b52ab' => '9a6924d0-dd7c-11e6-8168-0026551b52ab',
        '150f5b08-de30-11e6-8168-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab0',
        '5243e737-de30-11e6-8168-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        'c57bbcc2-de31-11e6-8168-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        '4ac58cf3-de33-11e6-8168-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        '9358b424-de30-11e6-8168-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        '9a590b3d-de30-11e6-8168-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        'a191442b-de30-11e6-8168-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        'a191442c-de30-11e6-8168-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        '77018e42-de33-11e6-8168-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        'a5298112-de33-11e6-8168-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        '9104976f-ff67-11e6-b31d-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        '9ac7c5f8-ff67-11e6-b31d-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        'a3092ca1-ff67-11e6-b31d-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        'c6224b90-ff67-11e6-b31d-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        'f85d1827-ff67-11e6-b31d-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        'cc7afbea-1dc7-11e7-bd7b-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        'ab3cae21-f745-11e6-b31d-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        'b412a4db-08b0-11e7-b31d-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        'dcd4201b-1dc7-11e7-bd7b-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
        'f3e6533a-1dc7-11e7-bd7b-0026551b52ab' => '150f5b08-de30-11e6-8168-0026551b52ab',
    ];

    /** @var bool  */
    protected $isProductHandle = false;

    /** @var array */
    protected $normalizedProducts = [];

    /** @var array */
    protected $normalizedProductsList = [];

    protected $properties = [];

    protected $fakeProperties = [
        'ef744fbd-ce81-11e6-82d0-902b346c0819' => [
            Category::EXT_ID_BED => [
                1 => '120*60',
                2 => '125*65',
                3 => 'трансформер'
            ]
        ],
        'ef744fb1-ce81-11e6-82d0-902b346c0819' => [
            Category::EXT_ID_BED => [
                4 => 'отсутствует',
                5 => 'автостенка',
                6 => 'планка'
            ]
        ],
        'ef744fab-ce81-11e6-82d0-902b346c0819' => [
            Category::EXT_ID_BED => [
                7 => 'поперечный маятник',
                8 => 'продольный маятник',
                9 => 'продольная качалка',
                10 => 'поперечная качалка',
                11 => 'неподвижная'
            ]
        ],
        'Опции' =>
        [
            Category::EXT_ID_BED => [
                12 => 'ящик',
                13 => 'колеса',
                14 => 'переделывается в диван'
            ],
            Category::EXT_ID_CARRIAGE => [
                15 => 'карманы',
                16 => 'наличие люльки',
                17 => 'перекидная ручка',
                18 => 'регулируемая ручка',
                19 => 'сомтровое окошко',
                20 => 'солнцезащитный козырек',
                21 => 'светоотражающие элементы',
                22 => 'подголовник люльки',
                23 => 'сумка',
                24 => 'корзинка'
            ]

        ],
        'c444f936-39f3-11e6-a2fd-0026551b52ab' =>
        [
            Category::EXT_ID_CARRIAGE => [
                25 => 'поворотные колеса спереди'
            ]
        ],
        '075866e6-39e4-11e6-a2fd-0026551b52ab' => [
            Category::EXT_ID_CARRIAGE => [
                26 => 'люлька',
                27 => 'два в одном',
                28 => 'три в одном',
                29 => 'трансформер',
                30 => 'прогулочная',
                31 =>'трость'
            ]
        ]
    ];

    /** @var array */
    protected $attributesGroups = [];
    /** @var array */
    protected $attributesGroupsIds = [];

    /**
     * @return bool
     */
    protected function isEvent() {
        $eventsKeys = array_keys($this->events);
        return $this->parser->nodeType == XMLReader::ELEMENT
        && in_array($this->parser->name, $eventsKeys);
    }

    /**
     * @return AttributeGroup[]
     */
    protected function getAttributesGroups() {
        if (empty($this->attributesGroups)) {
            $attributesGroups = AttributeGroup::model()->findAll();
            foreach ($attributesGroups as $item) {
                $this->attributesGroups[$item->ext_id] = $item;
            }
        }
        return $this->attributesGroups;
    }

    protected function getAttributesGroupsIds() {
        if (empty($this->attributesGroupsIds)) {
            $attributesGroups = $this->getAttributesGroups();
            $attributesIds = [];
            foreach ($attributesGroups as $item) {
                if (empty($item->ext_id)) {
                    $attributesIds[] = $item->name;
                } else {
                    $attributesIds[] = $item->ext_id;
                }
            }
            $this->attributesGroupsIds = $attributesIds;
        }
        return $this->attributesGroupsIds;
    }

    /**
     * @return AttributeGroup[]
     */
    protected function getCatalogAttributesGroups() {
        $attributesGroups = $this->getAttributesGroups();
        $catalogAttributesGroups = [];
        foreach ($attributesGroups as $item) {
            $catalogAttributesGroups[$item->ext_id] = $item;
        }
        return $catalogAttributesGroups;
    }

    /**
     * @return AttributeGroup[]
     */
    protected function getCheckboxAttributesGroups() {
        $attributesGroups = $this->getAttributesGroups();
        $checkboxAttributesGroups = [];
        foreach ($attributesGroups as $item) {
            if ($item->type == static::CHECKBOX_ATTRIBUTE_GROUP_TYPE) {
                $checkboxAttributesGroups[$item->ext_id] = $item;
            }
        }
        return $checkboxAttributesGroups;
    }

    /**
     * @return AttributeGroup[]
     */
    protected function getNumberAttributesGroups() {
        $attributesGroups = $this->getAttributesGroups();
        $numberAttributesGroups = [];
        foreach ($attributesGroups as $item) {
            if ($item->type == static::NUMBER_ATTRIBUTE_GROUP_TYPE) {
                $numberAttributesGroups[$item->ext_id] = $item;
            }
        }
        return $numberAttributesGroups;
    }

    /**
     * @return array
     */
    public function getOptionsAttributesIds() {
        return [
          static::WHEELS_PROPERTY_ID,
          static::BOX_PROPERTY_ID,
          static::POCKET_PROPERTY_ID,
          static::THROWN_HANDLE_PROPERTY_ID,
          static::SETTLED_HANDLE_PROPERTY_ID,
          static::VIEWED_WINDOW_PROPERTY_ID,
          static::SUN_SECURITY_PEAK_WINDOW_PROPERTY_ID,
          static::LIGHT_REFLECTING_ELEMENT_PROPERTY_ID,
          static::NICK_CUSHION_BUCKET_PROPERTY_ID
        ];
    }

    /**
     * @return array
     */
    protected function getActiveProperties() {
        return [
            static::COLOUR_PROPERTY_ID => static::COLOUR_PROPERTY_NAME,
            static::COLOUR_BED_PROPERTY_ID => static::COLOUR_PROPERTY_NAME,
            static::SITENAME_PROPERTY_ID => static::SITENAME_PROPERTY_NAME,
            static::WEIGHT_PROPERTY_ID => static::WEIGHT_PROPERTY_NAME
        ];
    }

    protected function handleEvent() {
        $this->isEventStopped = false;
        if ($this->isEvent()) {
            $this->currentEventKey = $this->parser->name;
            $currentEventName = $this->events[$this->currentEventKey];
            call_user_func([$this, $currentEventName]);
        }
    }

    /**
     * @return bool
     */
    protected function isHandleEvent() {

        if (
            !$this->isEventStopped
            && $this->parser->read()
            && !(
                $this->level == 0
                && $this->currentEventKey == $this->parser->name
                && $this->parser->nodeType == XMLReader::END_ELEMENT
            )
        ) {
            return true;
        }
        return false;
    }

    protected function onGroups() {
        $parentId = null;
        $currentGroup = null;
        while( $this->isHandleEvent() ) {
            if (
                $this->parser->nodeType == XMLReader::ELEMENT
            ) {
                if ( $this->parser->name == static::GROUP_TAG_NAME ) {
                    $currentGroup = ['parentId' => $parentId];
                }

                if ( $this->parser->name == static::ID_TAG_NAME ) {
                    $currentGroup['id'] = $this->parser->readInnerXml();
                }

                if ( $this->parser->name == static::NAME_TAG_NAME ) {
                    $currentGroup['name'] = $this->parser->readInnerXml();
                }

                if ( $this->parser->name == static::GROUPS_TAG_NAME ) {
                    ++$this->level;
                    $parentId = $currentGroup['id'];
                    if (empty($path)) {
                        $path = [];
                    }
                    $id = $currentGroup['id'];
                    $path[$id] = $currentGroup;
                }
            }

            if (
                $this->parser->nodeType == XMLReader::END_ELEMENT
            ) {
                if ( $this->parser->name == static::GROUP_TAG_NAME ) {
                    if (empty($this->groups)) {
                        $this->groups = [];
                    }
                    $id = $currentGroup['id'];
                    if (in_array($id, $this->activeCategoriesTree)) {
                        $this->groups[$id] = $currentGroup;
                    }
                }

                if ( $this->parser->name == static::GROUPS_TAG_NAME ) {
                    --$this->level;
                    $currentGroup = array_pop($path);
                    $parentId = $currentGroup['parentId'];
                }
            }
        }
    }

    protected function onProducts() {

        while( $this->isHandleEvent() ) {

            if (
                $this->parser->nodeType == XMLReader::ELEMENT
            ) {

                if ( $this->parser->name == static::PRODUCT_TAG_NAME ) {
                    $this->currentProduct = [];
                    $this->isProductHandle = true;
                }

                if (!$this->isProductHandle) {
                    continue;
                }

                if ( $this->parser->name == static::ID_TAG_NAME ) {

                    if (!empty($isGroup)) {
                        $xml_id = trim($this->parser->readInnerXml());
                        $this->isProductHandle = false;
                        if (in_array($xml_id, $this->activeCategories)) {
                            $this->isProductHandle = true;
                            $this->currentProduct['parent_id'] = $this->activeCategoriesTree[trim($this->parser->readInnerXml())];
                        }
                        $isGroup = false;
                    } else {
                        if (empty($this->currentProduct['id'])) {
                            $this->currentProduct['id'] = trim($this->parser->readInnerXml());
                        }
                    }
                }

                if ( $this->parser->name == static::ART_TAG_NAME ) {
                    if (empty($this->currentProduct['articul'])) {
                        $this->currentProduct['articul'] = trim($this->parser->readInnerXml());
                    }
                }

                if ( $this->parser->name == static::NAME_TAG_NAME ) {
                    if (empty($this->currentProduct['name'])) {
                        $this->currentProduct['name'] = trim($this->parser->readInnerXml());
                    }
                }

                if ( $this->parser->name == static::GROUPS_TAG_NAME ) {
                    $isGroup = true;
                }

                if ( $this->parser->name == static::DESCRIPTION_TAG_NAME ) {
                    if (empty($this->currentProduct['description'])) {
                        $this->currentProduct['description'] = trim($this->parser->readInnerXml());
                    }
                }

                if (
                    $this->parser->name == static::PROPERTIES_VALUES
                ) {
                    if ($this->isFake) {
                        $this->generateFakeProductAttributes();
                    } else {
                        $eventItem = [];
                        $eventItem['isEventStopped'] = $this->isEventStopped;
                        $eventItem['level'] = $this->level;
                        $eventItem['currentEventKey'] = $this->currentEventKey;
                        $this->handleEvent();
                        $this->isEventStopped = $eventItem['isEventStopped'];
                        $this->level = $eventItem['level'];
                        $this->currentEventKey = $eventItem['currentEventKey'];
                    }
                }
            }

            if (
                $this->parser->nodeType == XMLReader::END_ELEMENT
            ) {
                if ( $this->parser->name == static::PRODUCT_TAG_NAME ) {
                    if ($this->isProductHandle) {
                        if (!empty($this->currentProduct['parent_id'])) {
                            $this->products[] = $this->currentProduct;
                        }
                    }

                    $this->isProductHandle = false;
                }

            }
        }
    }

    protected function generateFakeProductAttributes() {
        $attributeGroups = $this->getAttributesGroups();
        $fakeAttributes = $this->fakeProperties;
        foreach ($attributeGroups as $item) {
            $attribute = [];
            if ($item->type == static::CHECKBOX_ATTRIBUTE_GROUP_TYPE) {
                if (!empty($fakeAttributes[$item->ext_id][$this->currentProduct['parent_id']])) {
                    $isChecked = mt_rand(0, 1);
                    if ($isChecked) {
                        foreach ($fakeAttributes[$item->ext_id][$this->currentProduct['parent_id']] as $key => $attributeItem) {
                            $attribute['id'] = $key;
                            $attribute['value'] = '';
                            $this->currentProduct['attributes'][$attribute['id']] = $attribute;
                        }
                    }
                }
            } elseif ($item->type == static::CATALOG_ATTRIBUTE_GROUP_TYPE) {
                if (!empty($fakeAttributes[$item->ext_id][$this->currentProduct['parent_id']])) {
                    $attributeKeys = [];
                    foreach ($fakeAttributes[$item->ext_id][$this->currentProduct['parent_id']] as $catalogAttributeKey => $catalogAttributeItem) {
                        $attributeKeys[] = $catalogAttributeKey;
                    }
                    $attributeKeysCount = count($attributeKeys);
                    $attribute['id'] = $attributeKeys[mt_rand(0, $attributeKeysCount - 1)];
                    $attribute['value'] = '';
                    $this->currentProduct['attributes'][$attribute['id']] = $attribute;
                }
            } elseif ($item->type == static::NUMBER_ATTRIBUTE_GROUP_TYPE) {
                if (!empty($fakeAttributes[$item->ext_id][$this->currentProduct['parent_id']])) {
                    foreach ($fakeAttributes[$item->ext_id][$this->currentProduct['parent_id']] as $key => $attributeItem) {
                        $attribute['id'] = $key;
                        $attribute['value'] = mt_rand(1, 99);
                        $this->currentProduct['attributes'][$attribute['id']] = $attribute;
                    }
                }
            } elseif ($item->type == static::OPTIONS_ATTRIBUTE_GROUP_TYPE) {
                if (!empty($fakeAttributes[static::OPTIONS_ATTRIBUTE_GROUP_NAME][$this->currentProduct['parent_id']])) {
                    $options = $fakeAttributes[static::OPTIONS_ATTRIBUTE_GROUP_NAME][$this->currentProduct['parent_id']];
                    foreach ($options as $key => $optionItem) {
                        $isChecked = mt_rand(0, 1);
                        if (!$isChecked) {
                            $attribute['id'] = $key;
                            $attribute['value'] = '';
                            $this->currentProduct['attributes'][$attribute['id']] = $attribute;
                        }
                    }
                }
            }
        }
    }

    protected function onPropertiesValues() {
        $isActiveDefaultProperty = false;
        $isActiveCustomProperty = false;
        $isActiveOptionsProperty = false;
        $id = false;
        while( $this->isHandleEvent() ) {
            if (
                $this->parser->nodeType == XMLReader::ELEMENT
            ) {
                if ( $this->parser->name == static::ID_TAG_NAME ) {
                    $this->properties[] = trim($this->parser->readInnerXml());
                    $id = trim($this->parser->readInnerXml());
                    $activeProperties = $this->getActiveProperties();
                    $activePropertiesKeys = array_keys($activeProperties);
                    if (in_array($id, $activePropertiesKeys)) {
                        $isActiveDefaultProperty = true;
                    }

                    $attributeGroups = $this->getAttributesGroups();
                    $attributeGroupsExtIds = array_keys($attributeGroups);
                    if (in_array($id, $attributeGroupsExtIds)) {
                        $isActiveCustomProperty = true;
                    }

                    $optionsExtIds = $this->getOptionsAttributesIds();
                    if (in_array($id, $optionsExtIds)) {
                        $isActiveOptionsProperty = true;
                    }
                }

                if ( $this->parser->name == static::PROPERTY_VALUE_TAG_NAME ) {
                    if ($isActiveDefaultProperty) {
                        $activeProperties = $this->getActiveProperties();
                        $this->currentProduct['properties'][$activeProperties[$id]] = trim($this->parser->readInnerXml());
                    }
                    if ($isActiveCustomProperty) {
                        $attribute = [];
                        $attributeValue = trim($this->parser->readInnerXml());
                        /** @var AttributeGroup[] $attributeGroups */
                        if (
                            $attributeGroups[$id]->type == static::CHECKBOX_ATTRIBUTE_GROUP_TYPE
                        ) {
                            if ($attributeValue == 'Да') {
                                $attribute['id'] = $id;
                                $attribute['value'] = '';
                            }
                        } elseif (
                            $attributeGroups[$id]->type == static::CATALOG_ATTRIBUTE_GROUP_TYPE
                        ) {
                            $attribute['id'] = $attributeValue;
                            $attribute['value'] = '';
                        } elseif (
                            $attributeGroups[$id]->type == static::NUMBER_ATTRIBUTE_GROUP_TYPE
                        ) {
                            $attribute['id'] = $id;
                            $attribute['value'] = str_replace(',', '.', $attributeValue);
                        }
                        if (!empty($attribute)) {
                            $this->currentProduct['attributes'][$id] = $attribute;
                        }
                    }
                    if ($isActiveOptionsProperty) {
                        $attribute = [];
                        $attributeValue = trim($this->parser->readInnerXml());
                        if ($attributeValue == 'Да') {
                            $attribute['id'] = $id;
                            $attribute['value'] = '';
                        }
                        if (!empty($attribute)) {
                            $this->currentProduct['attributes'][$id] = $attribute;
                        }
                    }
                }
            }

            if (
                $this->parser->nodeType == XMLReader::END_ELEMENT
            ) {
                if ( $this->parser->name == static::PROPERTY_VALUES ) {
                    $isActiveDefaultProperty = false;
                    $isActiveCustomProperty = false;
                    $isActiveOptionsProperty = false;
                }
            }
        }
    }

    protected function onPropertyDescription() {
        $catalogAttributesGroups = $this->getCatalogAttributesGroups();
        $catalogAttributesGroupsIds = array_keys($catalogAttributesGroups);
        $checkboxAttributesGroups = $this->getCheckboxAttributesGroups();
        $checkboxAttributesGroupsIds = array_keys($checkboxAttributesGroups);
        $numberAttributesGroups = $this->getNumberAttributesGroups();
        $numberAttributesGroupsIds = array_keys($numberAttributesGroups);
        $optionsAttributesIds = $this->getOptionsAttributesIds();
        $isActiveCatalogAttribute = false;
        $isActiveCheckboxAttribute = false;
        $isActiveNumberAttribute = false;
        $isActiveOptionAttribute = false;
        $isValuesVariants = false;
        $isCatalog = false;
        $generalAttributes = [];
        $isActiveProperty = false;
        $currentField = '';
        while( $this->isHandleEvent() ) {
            if (
                $this->parser->nodeType == XMLReader::ELEMENT
            ) {
                if ( $this->parser->name == static::ID_TAG_NAME ) {
                    $id = trim($this->parser->readInnerXml());
                    if (in_array($id, $catalogAttributesGroupsIds)) {
                        $isActiveCatalogAttribute = true;
                    }

                    if (in_array($id, $checkboxAttributesGroupsIds)) {
                        $isActiveCheckboxAttribute = true;
                    }

                    if (in_array($id, $numberAttributesGroupsIds)) {
                        $isActiveNumberAttribute = true;
                    }

                    if (in_array($id, $optionsAttributesIds)) {
                        $isActiveOptionAttribute = true;
                    }

                    if (
                        $isActiveCheckboxAttribute
                        || $isActiveNumberAttribute
                        || $isActiveOptionAttribute
                    ) {

                        $generalAttributes = [
                            'id' => $id,
                            'value' => ''
                        ];

                    }

                    $activeProperties = $this->getActiveProperties();
                    $ids = array_keys($activeProperties);
                    if (in_array($id, $ids)) {
                        $currentField = $activeProperties[$id];
                        $isActiveProperty = true;
                    }
                }

                if ( $this->parser->name == static::VALUES_VARIANTS_TAG_NAME ) {
                    $isValuesVariants = true;
                }

                if ( $this->parser->name == static::CATALOG_TAG_NAME ) {
                    $isCatalog = true;
                }

                if (
                    $isValuesVariants
                    && ($isActiveCatalogAttribute || $isActiveProperty)
                    && $isCatalog
                ) {
                    if ( $this->parser->name == static::PROPERTY_ID_TAG_NAME ) {
                        $generalAttributes['id'] = trim($this->parser->readInnerXml());
                    }
                    if ( $this->parser->name == static::PROPERTY_VALUE_TAG_NAME ) {
                        $generalAttributes['value'] = trim($this->parser->readInnerXml());
                    }
                }

                if (
                    $isActiveCheckboxAttribute
                    || $isActiveNumberAttribute
                    || $isActiveOptionAttribute
                ) {
                    if ( $this->parser->name == static::NAME_TAG_NAME ) {
                        $generalAttributes['value'] = trim($this->parser->readInnerXml());
                    }
                }
            }

            if (
                $this->parser->nodeType == XMLReader::END_ELEMENT
            ) {
                if ( $this->parser->name == static::VALUES_VARIANTS_TAG_NAME ) {
                    $isValuesVariants = false;
                }

                if ( $this->parser->name == static::CATALOG_TAG_NAME ) {
                    if ($isActiveCatalogAttribute) {
                        /** @var string $id */
                        $this->catalogAttributes[$generalAttributes['id']] = $generalAttributes;
                        $this->catalogAttributes[$generalAttributes['id']]['attribute_group_id'] = $id;
                    }
                    if ($isActiveProperty) {
                        /** @var string $id */
                        $this->{$currentField}[] = $generalAttributes;
                    }
                    $generalAttributes = [];
                    $isCatalog = false;
                }

                if ( $this->parser->name == static::PROPERTY_TAG_NAME ) {
                    $isActiveProperty = false;
                    $isActiveCatalogAttribute = false;

                    if (
                        $isActiveCheckboxAttribute
                        || $isActiveNumberAttribute
                    ) {
                        /** @var string $id */
                        $this->catalogAttributes[$generalAttributes['id']] = $generalAttributes;
                        $this->catalogAttributes[$generalAttributes['id']]['attribute_group_id'] = $id;
                    }

                    if ($isActiveOptionAttribute) {
                        $this->optionsAttributes[$generalAttributes['id']] = $generalAttributes;
                    }

                    $isActiveCheckboxAttribute = false;
                    $isActiveNumberAttribute = false;
                    $isActiveOptionAttribute = false;
                }
            }
        }
    }

    protected function stopEvent() {
        $this->isEventStopped = true;
    }

    /**
     * @param $xml
     * @param null $encoding
     * @throws CException
     */
    protected function startXML($xml, $encoding = null) {
        $this->parser = new XMLReader();
        if (!$this->parser->open($xml, $encoding, LIBXML_COMPACT))
            throw new CException("Can't open XML file: $xml");
    }

    protected function generateNormalizedProducts() {
        $normalizedProducts = [];
        $normalizedProductsList = [];
        foreach ($this->products as $item) {
            if (empty($item['properties'][static::SITENAME_PROPERTY_NAME])) {
                $normalizedProducts[$item['name']][$item['id']] = $item;
            } else {
                $normalizedProducts[$item['properties'][static::SITENAME_PROPERTY_NAME]][$item['id']] = $item;
            }
        }
        foreach ($normalizedProducts as $key => $item) {
            $currentProd = $item;
            ksort($currentProd);
            $normalizedProducts[$key] = $currentProd;
            $currentProd = current($currentProd);
            $currentProdId = $currentProd['id'];
            $normalizedProductsList[$currentProdId] = $currentProd;
            $normalizedProductsList[$currentProdId]['name'] = $key;
        }

        $this->normalizedProducts = $normalizedProducts;
        $this->normalizedProductsList = $normalizedProductsList;
    }

    /**
     * @param $xml
     */
    public function actionImport($xml, $dbfolder, $isfake) {

        $this->isFake = intval($isfake);
        $this->dbfolder = $dbfolder;

        $this->startXML($xml, 'UTF-8');

        echo "Start parsing!\n";
        while( $this->parser->read() ) {
            $this->handleEvent();
        }
        $this->closeXML();
        echo "End parsing!\n";

        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            echo "Start writing to db_infile!\n";

            echo "Start writing categories!\n";
            $this->writeCategoriesInfile();
            echo "End writing categories!\n";

            echo "Start writing colours!\n";
            $this->writeColoursInfile();
            echo "End writing colours!\n";

            echo "Start writing catalog attributes!\n";
            $this->writeCatalogAttributesInfile();
            echo "End writing catalog attributes!\n";

            echo "Start writing options attributes!\n";
            $this->writeOptionsAttributesInfile();
            echo "End writing options attributes!\n";

            echo "End writing to db_infile!\n";

            echo "Start creating category temporary table!\n";
            $this->createCategoryTemporaryTable();
            echo "End creating category temporary table!\n";

            echo "Start creating colour temporary table!\n";
            $this->createColourTemporaryTable();
            echo "End creating colour temporary table!\n";

            if (!$this->isFake) {
                echo "Start creating attribute temporary table!\n";
                $this->createCatalogAttributeTemporaryTable();
                echo "End creating attribute temporary table!\n";

                echo "Start creating options attribute temporary table!\n";
                $this->createOptionsAttributeTemporaryTable();
                echo "End creating options attribute temporary table!\n";
            }

            echo "Start creating catalog attribute to product temporary table!\n";
            $this->createCatalogAttributeToProductTemporaryTable();
            echo "End creating catalog attribute to product temporary table!\n";

            echo "Start creating catalog attribute to product temporary table for new product!\n";
            $this->createNewProductCatalogAttributeToProductTemporaryTable();
            echo "End creating catalog attribute to product temporary table for new product!\n";

            echo "Start save db_infile to category!\n";
            $this->saveCategoriesInfileToTemporaryTable();
            $this->saveNewCategories();
            echo "End save db_infile to category!\n";

            echo "Start save db_infile to colour!\n";
            $this->saveColoursInfileToTemporaryTable();
            $this->saveNewColours();
            echo "End save db_infile to colour!\n";


            if ($this->isFake) {
                echo "Start save db_infile to catalog attribute!\n";
                $this->saveNewCatalogAttributes();
                echo "End save db_infile to catalog attribute!\n";

                echo "Start save db_infile to options attribute!\n";
                $this->saveNewOptionsAttributes();
                echo "End save db_infile to options attribute!\n";
            } else {
                echo "Start save db_infile to catalog attribute!\n";
                $this->saveCatalogAttributeInfileToTemporaryTable();
                $this->saveNewCatalogAttributes();
                echo "End save db_infile to catalog attribute!\n";

                echo "Start save db_infile to options attribute!\n";
                $this->saveOptionsAttributeInfileToTemporaryTable();
                $this->saveNewOptionsAttributes();
                echo "End save db_infile to options attribute!\n";
            }

            echo "Start generation of normalized products!\n";
            $this->generateNormalizedProducts();
            echo "End generation of normalized products!\n";

            echo "Start writing to db_infile!\n";


            echo "Start writing catalog attributes to product!\n";
            $this->writeCatalogAttributesToProductInfile();
            echo "End writing catalog attributes to product!\n";


            echo "Start writing normalized products!\n";
            $this->writeNormalizedProductsInfile();
            echo "End writing normalized products!\n";

            echo "End writing to db_infile!\n";

            echo "Start creating product temporary table!\n";
            $this->createProductTemporaryTable();
            echo "End creating product temporary table!\n";


            echo "Start save db_infile to catalog attribute to product!\n";
            $this->saveCatalogAttributeToProductInfileToTemporaryTable();
            $this->saveNewProductCatalogAttributeToProductInfileToTemporaryTable();
            echo "End save db_infile to catalog attribute to product!\n";



            echo "Start save db_infile to temporary_product!\n";
            $this->saveProductsInfileToTemporaryTable();
            $this->saveNewProducts();
            echo "End save db_infile to temporary_product!\n";

            echo "Start save new catalog attribute to product!\n";
            $this->saveNewCatalogAttributesToProducts();
            echo "End save new catalog attribute to product!\n";

            echo "Start creating product variant temporary table!\n";
            $this->createProductVariantTemporaryTable();
            echo "End creating product variant temporary table!\n";

            echo "Start writing products variants!\n";
            $this->writeProductsVariantsInfile();
            echo "End writing products variants!\n";

            echo "Start save db_infile to temporary_product_variant!\n";
            $this->saveProductsVariantsInfileToTemporaryTable();
            $this->saveNewProductsVariants();
            echo "End save db_infile to temporary_product_variant!\n";
            $transaction->commit();
        } catch (Exception $e) {
            echo 'There is an error. All changes are rollbacked!'.$e->getMessage();
            $transaction->rollback();
        }


        /*
         * /home/vagrant/project/protected/migrations/vagrant_test.sql
         */
        /*try
        {
            /*$db = Yii::app()->getDb();*/
            /*$sql = "
            LOAD DATA
            INFILE '".$dbfile."'
            INTO TABLE category(name, parent_id);";
            Yii::app()->db->createCommand($sql)->query();*/
            /*$this->connectToMySQL();
            var_dump($this->mysql);
            if (!($stmt = $this->mysql->query($sql))) {
                echo "\nQuery execute failed: ERRNO: (" . $this->mysql->errno . ") " . $this->mysql->error;
            };
            $this->mysql->close();*/
        /*} catch (Exception $e) {
            echo $e->getMessage();
        }*/

    }

    protected function importToDB() {
        $db = $this->connectToMySQL();
    }

    protected function connectToMySQL() {
        $db = Yii::app()->getDb();
        $this->mysql = new mysqli('localhost', 'vagrant', 'vagrant', 'vagrant');
    }

    protected function closeXML() {
        $this->parser->close();
    }

    protected function writeCategoriesInfile() {
        $categories = $this->groups;
        $file = $this->dbfolder.'/'.static::CATEGORIES_INFILE_NAME.'.sql';
        if (file_exists($file)) {
            unlink($file);
        }
        $fileString = '';
        foreach ($categories as $item) {
            $fileString .= $item['id']."\t";
            $fileString .= $item['name']."\t";
            if (empty($item['parentId'])) {
                $parentIdStr = '\N';
            } else {
                $parentIdStr = $item['parentId'];
            }
            $fileString .= $parentIdStr."\t";
            $fileString .= "\n";
        }
        file_put_contents($file, $fileString);
    }

    protected function saveCategoriesInfileToTemporaryTable() {
        $file = $this->dbfolder.'/'.static::CATEGORIES_INFILE_NAME.'.sql';
        try
        {
            $sql = "
            LOAD DATA
            INFILE '".$file."'
            INTO TABLE temporary_category(ext_id, name, parent_ext_id);";
            Yii::app()->db->createCommand($sql)->query();
        } catch (Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
        } finally {
            unlink($file);
        }
    }

    protected function saveColoursInfileToTemporaryTable() {
        $file = $this->dbfolder.'/'.static::COLOURS_INFILE_NAME.'.sql';
        try
        {
            $sql = "
            LOAD DATA
            INFILE '".$file."'
            INTO TABLE temporary_colour(ext_id, name);";
            Yii::app()->db->createCommand($sql)->query();
        } catch (Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
        } finally {
            unlink($file);
        }
    }

    protected function saveProductsCategoriesInfileToTemporaryTable() {
        $file = $this->dbfolder.'/'.static::PRODUCTS_INFILE_NAME.'.sql';
        $sql = "
        LOAD DATA
        INFILE '".$file."'
        INTO TABLE temporary_product_category(ext_id, name, articul, description, parent_ext_id, colour_ext_id);";
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function saveNewProductsCategoriesToTemporaryTable() {
        $sql = 'INSERT INTO temporary_new_product_category(ext_id, category_id)
            (
              SELECT tnp.ext_id, c.id
              FROM temporary_new_product tnp
              INNER JOIN category c
              ON tnp.parent_ext_id = c.ext_id
            )';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function saveNewProductsColoursToTemporaryTable() {
        $sql = 'INSERT INTO temporary_new_product_colour(ext_id, colour_id)
            (
              SELECT tnp.ext_id, cl.id
              FROM temporary_new_product tnp
              LEFT JOIN colour cl
              ON tnp.colour_ext_id = cl.ext_id
            )';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function saveProductsInfileToTemporaryTable() {
        $file = $this->dbfolder.'/'.static::PRODUCTS_INFILE_NAME.'.sql';
        try
        {
            $sql = "
            LOAD DATA
            INFILE '".$file."'
            INTO TABLE temporary_product(ext_id, name, description, category_id, weight, width, length, height);";
            Yii::app()->db->createCommand($sql)->query();
        } catch (Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
        } finally {
            unlink($file);
        }
    }

    protected function saveProductsVariantsInfileToTemporaryTable() {
        $file = $this->dbfolder.'/'.static::NORMALIZED_INFILE_NAME.'.sql';
        try
        {
            $sql = "
            LOAD DATA
            INFILE '".$file."'
            INTO TABLE temporary_product_variant(ext_id, articul, colour_id, product_id);";
            Yii::app()->db->createCommand($sql)->query();
        } catch (Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
        } finally {
            unlink($file);
        }
    }

    protected function saveNewCatalogAttributesToProducts() {
        if ($this->isFake) {
            $attributeField = 'attribute_id';
            $mainAttributeField = 'id';
        } else {
            $attributeField = 'attribute_ext_id';
            $mainAttributeField = 'ext_id';
        }

        $sql = 'INSERT INTO product_attribute(product_id, attribute_id, value)
        (
          SELECT p.id, a.id, tnpcatp.value
          FROM product p
          INNER JOIN temporary_new_product_catalog_attribute_to_product tnpcatp
          ON tnpcatp.product_ext_id = p.ext_id
          INNER JOIN attribute a
          ON a.'.$mainAttributeField.' = tnpcatp.'.$attributeField.'
        )';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function saveProductsVariantsToStoragesToTemporaryTable() {
        $file = $this->dbfolder.'/'.static::PRODUCTS_TO_STORAGES_INFILE_NAME.'.sql';
        try
        {
            $sql = "
            LOAD DATA
            INFILE '".$file."'
            INTO TABLE temporary_product_variant_to_storage(product_variant_ext_id, storage_ext_id, quantity);";
            Yii::app()->db->createCommand($sql)->query();
        } catch (Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
        } finally {
            unlink($file);
        }
    }

    protected function saveProductVariantPriceInfileToTemporaryTable() {
        $file = $this->dbfolder.'/'.static::OFFERS_INFILE_NAME.'.sql';
        try
        {
            $sql = "
            LOAD DATA
            INFILE '".$file."'
            INTO TABLE temporary_product_variant_price(product_variant_ext_id, price);";
            Yii::app()->db->createCommand($sql)->query();
        } catch (Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
        } finally {
            unlink($file);
        }
    }

    protected function saveStoragesInfileToTemporaryTable() {
        $file = $this->dbfolder.'/'.static::STORAGES_INFILE_NAME.'.sql';
        try
        {
            $sql = "
            LOAD DATA
            INFILE '".$file."'
            INTO TABLE temporary_storage(ext_id, name);";
            Yii::app()->db->createCommand($sql)->query();
        } catch (Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
        } finally {
            unlink($file);
        }
    }

    protected function saveCatalogAttributeToProductInfileToTemporaryTable() {
        $file = $this->dbfolder.'/'.static::CATALOG_ATTRIBUTES_TO_PRODUCTS_INFILE_NAME.'.sql';
        if ($this->isFake) {
            $attributeField = 'attribute_id';
        } else {
            $attributeField = 'attribute_ext_id';
        }
        try
        {
            $sql = "
            LOAD DATA
            INFILE '".$file."'
            INTO TABLE temporary_catalog_attribute_to_product(product_ext_id, ".$attributeField.", value);";
            Yii::app()->db->createCommand($sql)->query();
        } catch (Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
        } finally {
            unlink($file);
        }
    }

    protected function saveCatalogAttributeInfileToTemporaryTable() {
        $file = $this->dbfolder.'/'.static::CATALOG_ATTRIBUTES_INFILE_NAME.'.sql';
        try
        {
            $sql = "
            LOAD DATA
            INFILE '".$file."'
            INTO TABLE temporary_catalog_attribute(ext_id, name, attribute_group_id);";
            Yii::app()->db->createCommand($sql)->query();
        } catch (Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
        } finally {
            unlink($file);
        }
    }

    protected function saveOptionsAttributeInfileToTemporaryTable() {
        $file = $this->dbfolder.'/'.static::OPTIONS_ATTRIBUTES_INFILE_NAME.'.sql';
        try
        {
            $sql = "
            LOAD DATA
            INFILE '".$file."'
            INTO TABLE temporary_options_attribute(ext_id, name, attribute_group_id);";
            Yii::app()->db->createCommand($sql)->query();
        } catch (Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
        } finally {
            unlink($file);
        }
    }

    protected function saveNewProductsToTemporaryTable() {
        $sql = 'INSERT INTO temporary_new_product(ext_id, name, articul, description, parent_ext_id, colour_ext_id, weight, width, length, height)
            (
              SELECT tp.ext_id, tp.name, tp.articul, tp.description, tp.parent_ext_id, tp.colour_ext_id, tp.weight, tp.width, tp.length, tp.height
              FROM temporary_product tp
              LEFT JOIN product p
              ON p.ext_id = tp.ext_id
              WHERE p.id IS NULL
            )';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createCategoryTemporaryTable() {
        $sql = '            
            CREATE TEMPORARY TABLE temporary_category(
              `id` INT NOT NULL AUTO_INCREMENT,
              `ext_id` VARCHAR(255) NULL,
              `name` VARCHAR(255) NULL,
              `parent_ext_id` VARCHAR(255) NULL,
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createColourTemporaryTable() {
        $sql = '            
            CREATE TEMPORARY TABLE temporary_colour(
              `id` INT NOT NULL AUTO_INCREMENT,
              `ext_id` VARCHAR(255) NULL,
              `name` VARCHAR(255) NULL,
              `colour_ext_id` VARCHAR(255) NULL,
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createCatalogAttributeTemporaryTable() {
        $sql = '            
            CREATE TEMPORARY TABLE temporary_catalog_attribute(
              `id` INT NOT NULL AUTO_INCREMENT,
              `ext_id` VARCHAR(255) NULL,
              `attribute_group_id` INT NOT NULL,
              `name` VARCHAR(255) NULL,              
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createOptionsAttributeTemporaryTable() {
        $sql = '            
            CREATE TEMPORARY TABLE temporary_options_attribute(
              `id` INT NOT NULL AUTO_INCREMENT,
              `ext_id` VARCHAR(255) NULL,
              `attribute_group_id` INT NOT NULL,
              `name` VARCHAR(255) NULL,              
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createStorageTemporaryTable() {
        $sql = '            
            CREATE TEMPORARY TABLE temporary_storage(
              `id` INT NOT NULL AUTO_INCREMENT,
              `ext_id` VARCHAR(255) NULL,
              `name` VARCHAR(255) NULL,
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createCatalogAttributeToProductTemporaryTable() {
        if ($this->isFake) {
            $attributeField = '`attribute_id` INT NULL';
        } else {
            $attributeField = '`attribute_ext_id` VARCHAR(255) NULL';
        }
        $sql = '            
            CREATE TEMPORARY TABLE temporary_catalog_attribute_to_product(
              `id` INT NOT NULL AUTO_INCREMENT,
              '.$attributeField.',
              `product_ext_id` VARCHAR(255) NULL,
              `value` DECIMAL(10, 2) NULL,
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    public function createNewProductCatalogAttributeToProductTemporaryTable() {
        if ($this->isFake) {
            $attributeField = '`attribute_id` INT NULL';
        } else {
            $attributeField = '`attribute_ext_id` VARCHAR(255) NULL';
        }
        $sql = '            
            CREATE TEMPORARY TABLE temporary_new_product_catalog_attribute_to_product(
              `id` INT NOT NULL AUTO_INCREMENT,
              '.$attributeField.',
              `product_ext_id` VARCHAR(255) NULL,
              `value` DECIMAL(10, 2) NULL,
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createProductVariantToStorageTemporaryTable() {
        $sql = '            
            CREATE TEMPORARY TABLE temporary_product_variant_to_storage(
              `id` INT NOT NULL AUTO_INCREMENT,
              `product_variant_ext_id` VARCHAR(255) NULL,
              `storage_ext_id` VARCHAR(255) NULL,
              `quantity` INT NULL,
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createProductVariantPriceTemporaryTable() {
        $sql = '            
            CREATE TEMPORARY TABLE temporary_product_variant_price(
              `id` INT NOT NULL AUTO_INCREMENT,
              `product_variant_ext_id` VARCHAR(255) NULL,
              `price` INT NULL,
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createNewProductTemporaryTable() {
        $sql = '            
            CREATE TEMPORARY TABLE temporary_new_product(
              `id` INT NOT NULL AUTO_INCREMENT,
              `ext_id` VARCHAR(255) NULL,
              `name` VARCHAR(255) NULL,
              `articul` VARCHAR(255) NULL,
              `description` TEXT NULL,
              `parent_ext_id` VARCHAR(255) NULL,
              `colour_ext_id` VARCHAR(255) NULL,
              `category_id` INT NULL,
              `weight` DECIMAL(10, 2) NULL,
              `width` INT NULL,
              `length` INT NULL,
              `height` INT NULL,
              UNIQUE KEY `ext_id` (`ext_id`),
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createProductTemporaryTable() {
        $sql = '            
            CREATE TEMPORARY TABLE temporary_product(
              `id` INT NOT NULL AUTO_INCREMENT,
              `ext_id` VARCHAR(255) NULL,
              `name` VARCHAR(255) NULL,
              `description` TEXT NULL,
              `category_id` INT NULL,
              `weight` DECIMAL(10, 2) NULL,
              `width` INT NULL,
              `length` INT NULL,
              `height` INT NULL,
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createProductVariantTemporaryTable() {
        $sql = '            
            CREATE TEMPORARY TABLE temporary_product_variant(
              `id` INT NOT NULL AUTO_INCREMENT,
              `ext_id` VARCHAR(255) NULL,
              `articul` VARCHAR(255) NULL,
              `product_id` INT NULL,
              `colour_id` INT NULL,
              UNIQUE KEY `ext_id` (`ext_id`),
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createNewProductCategoryTemporaryTable() {
        $sql = '            
            CREATE TEMPORARY TABLE temporary_new_product_category(
              `id` INT NOT NULL AUTO_INCREMENT,
              `ext_id` VARCHAR(255) NULL,
              `category_id` INT NULL,
              UNIQUE KEY `ext_id` (`ext_id`),
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function createNewProductColourTemporaryTable() {
        $sql = '            
            CREATE TEMPORARY TABLE temporary_new_product_colour(
              `id` INT NOT NULL AUTO_INCREMENT,
              `ext_id` VARCHAR(255) NULL, 
              `colour_id` INT NULL,
              UNIQUE KEY `ext_id` (`ext_id`),
              PRIMARY KEY (`id`)
            );
        ';
        Yii::app()->db->createCommand($sql)->query();
    }

    protected function saveNewCategories() {
        $db = Yii::app()->db;
        $sql = '
            SELECT
                  t.ext_id AS ext_id
                FROM
                  temporary_category t
                LEFT JOIN
                  category c ON c.ext_id = t.ext_id
                WHERE
                  c.id IS NULL;
        ';
        $command = $db->createCommand($sql);
        $newExtIds = [];
        $rows = $command->queryAll();
        foreach ($rows as $val) {
            $newExtIds[] = $val['ext_id'];
        }

        if (!empty($newExtIds)) {
            $sql = 'INSERT INTO
              category(name, ext_id) (
                SELECT
                  t.name, t.ext_id
                FROM
                  temporary_category t
                WHERE 
                  t.ext_id IN ("' . implode('", "', $newExtIds) . '")
                  );
                ';
            Yii::app()->db->createCommand($sql)->query();

            $sql = '
            REPLACE INTO category(name, ext_id, parent_id)
            (
              SELECT tc.name, tc.ext_id, c.id
              FROM temporary_category tc
              INNER JOIN category c
              ON tc.parent_ext_id = c.ext_id
              WHERE tc.ext_id IN ("'.implode('", "', $newExtIds).'")
            )
            ';
            $db->createCommand($sql)->query();
        }
    }

    protected function saveNewColours() {
        $db = Yii::app()->db;
        $sql = '
            INSERT INTO
              colour
              (name, ext_id) (
                SELECT
                  t.name, t.ext_id
                FROM
                  temporary_colour t
                LEFT JOIN
                  colour c ON c.ext_id = t.ext_id
                WHERE
                  c.id IS NULL);
        ';
        $db->createCommand($sql)->query();
    }

    protected function saveNewStorages() {
        $db = Yii::app()->db;
        $sql = '
            INSERT INTO
              storage
              (name, ext_id) (
                SELECT
                  t.name, t.ext_id
                FROM
                  temporary_storage t
                LEFT JOIN
                  storage s ON s.ext_id = t.ext_id
                WHERE
                  s.id IS NULL);
        ';
        $db->createCommand($sql)->query();
    }

    protected function saveNewProductCatalogAttributeToProductInfileToTemporaryTable() {
        $db = Yii::app()->db;
        if ($this->isFake) {
            $attributeField = 'attribute_id';
        } else {
            $attributeField = 'attribute_ext_id';
        }
        $sql = '
            INSERT INTO
              temporary_new_product_catalog_attribute_to_product
              (product_ext_id, '.$attributeField.', value) (
                SELECT
                  tcatp.product_ext_id, tcatp.'.$attributeField.', tcatp.value
                FROM
                  temporary_catalog_attribute_to_product tcatp
                LEFT JOIN
                  product p ON p.ext_id = tcatp.product_ext_id
                WHERE
                  p.id IS NULL);
        ';
        $db->createCommand($sql)->query();
    }

    protected function saveNewProductVariantPrice() {
        $db = Yii::app()->db;
        $sql = '
            REPLACE INTO
              product_variant
              (ext_id, articul, product_id, colour_id, unit_price) (
                SELECT
                  pv.ext_id, pv.articul, pv.product_id, pv.colour_id, pvp.price
                FROM
                  product_variant pv
                INNER JOIN
                  temporary_product_variant_price pvp 
                ON pv.ext_id = pvp.product_variant_ext_id
              );
        ';
        $db->createCommand($sql)->query();
    }

    protected function saveNewProducts() {

        $sql = '
                INSERT INTO
                product(ext_id, name, description, category_id, weight, width, length, height)
                (
                  SELECT tp.ext_id, tp.name, tp.description, tp.category_id, tp.weight, tp.width, tp.length, tp.height
                  FROM temporary_product tp
                  LEFT JOIN product p
                  ON p.name = tp.name
                  WHERE p.id IS NULL
                );';

        Yii::app()->db->createCommand($sql)->query();
    }

    protected function saveNewCatalogAttributes() {
        if ($this->isFake) {
            $file = $this->dbfolder.'/'.static::CATALOG_ATTRIBUTES_INFILE_NAME.'.sql';
            try
            {
                $sql = "
            LOAD DATA
            INFILE '".$file."'
            INTO TABLE attribute(id, name, attribute_group_id);";
                Yii::app()->db->createCommand($sql)->query();
            } catch (Exception $e) {
                echo "Exception: ".$e->getMessage()."\n";
            } finally {
                unlink($file);
            }
        } else {
            $sql = '
                INSERT INTO attribute
                (name, ext_id, attribute_group_id)
                (
                  SELECT tca.name, tca.ext_id, tca.attribute_group_id
                  FROM temporary_catalog_attribute tca
                  LEFT JOIN attribute a
                  ON a.ext_id = tca.ext_id
                  WHERE a.id IS NULL
                );';

            Yii::app()->db->createCommand($sql)->query();
        }
    }

    protected function saveNewOptionsAttributes() {
        if ($this->isFake) {
            $file = $this->dbfolder.'/'.static::OPTIONS_ATTRIBUTES_INFILE_NAME.'.sql';
            try
            {
                $sql = "
            LOAD DATA
            INFILE '".$file."'
            INTO TABLE attribute(id, name, attribute_group_id);";
                Yii::app()->db->createCommand($sql)->query();
            } catch (Exception $e) {
                echo "Exception: ".$e->getMessage()."\n";
            } finally {
                unlink($file);
            }
        } else {
            $sql = '
                INSERT INTO attribute
                (name, ext_id, attribute_group_id)
                (
                  SELECT toa.name, toa.ext_id, toa.attribute_group_id
                  FROM temporary_options_attribute toa
                  LEFT JOIN attribute a
                  ON a.ext_id = toa.ext_id
                  WHERE a.id IS NULL
                );';

            Yii::app()->db->createCommand($sql)->query();
        }
    }


    protected function saveNewProductsVariants() {

        $sql = '
                INSERT INTO
                product_variant(ext_id, articul, colour_id, product_id)
                (
                  SELECT tpv.ext_id, tpv.articul, tpv.colour_id, tpv.product_id
                  FROM temporary_product_variant tpv
                  LEFT JOIN product_variant pv
                  ON pv.ext_id = tpv.ext_id
                  WHERE pv.id IS NULL
                );';

        Yii::app()->db->createCommand($sql)->query();
    }

    protected function saveNewProductsVariantsToStorages() {

        $sql = 'TRUNCATE TABLE storage_has_product';

        Yii::app()->db->createCommand($sql)->query();

        $sql = '
                REPLACE INTO
                storage_has_product(product_id, storage_id, count)
                (
                  SELECT pv.id, s.id, tpts.quantity
                  FROM product_variant pv
                  INNER JOIN temporary_product_variant_to_storage tpts
                  ON tpts.product_variant_ext_id = pv.ext_id
                  INNER JOIN storage s
                  ON tpts.storage_ext_id = s.ext_id
                );';

        Yii::app()->db->createCommand($sql)->query();
    }

    protected function writeColoursInfile() {
        $colours = $this->colours;
        $file = $this->dbfolder.'/'.static::COLOURS_INFILE_NAME.'.sql';
        if (file_exists($file)) {
            unlink($file);
        }
        $fileString = '';
        foreach ($colours as $item) {
            $fileString .= $item['id']."\t";
            $fileString .= $item['value']."\t";
            $fileString .= "\n";
        }
        file_put_contents($file, $fileString);
    }

    protected function writeStoragesInfile() {
        $storages = $this->storages;
        $file = $this->dbfolder.'/'.static::STORAGES_INFILE_NAME.'.sql';
        if (file_exists($file)) {
            unlink($file);
        }
        $fileString = '';
        foreach ($storages as $item) {
            $fileString .= $item['id']."\t";
            $fileString .= $item['name']."\t";
            $fileString .= "\n";
        }
        file_put_contents($file, $fileString);
    }

    protected  function writeCatalogAttributesInfile() {
        $catalogAttributes = $this->catalogAttributes;
        $file = $this->dbfolder.'/'.static::CATALOG_ATTRIBUTES_INFILE_NAME.'.sql';
        if (file_exists($file)) {
            unlink($file);
        }
        $fileString = '';
        $catalogAttributesGroups = $this->getCatalogAttributesGroups();
        $existedKeys = [];
        if ($this->isFake) {
            foreach ($this->fakeProperties as $key => $item) {
                if ($key != static::OPTIONS_ATTRIBUTE_GROUP_NAME) {
                    $currentAttributeGroup = $catalogAttributesGroups[$key];
                    foreach ($item as $categoryKey => $categoryAttributes) {
                        foreach ($categoryAttributes as $attributeKey => $attribute) {
                            if (!in_array($attributeKey, $existedKeys)) {
                                $existedKeys[] = $attributeKey;
                                $fileString .= $attributeKey."\t";
                                $fileString .= $attribute."\t";
                                $fileString .= $currentAttributeGroup->id."\t";
                                $fileString .= "\n";
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($catalogAttributes as $item) {
                $currentAttributeGroup = $catalogAttributesGroups[$item['attribute_group_id']];
                $fileString .= $item['id']."\t";
                $fileString .= $item['value']."\t";
                $fileString .= $currentAttributeGroup->id."\t";
                $fileString .= "\n";
            }
        }
        file_put_contents($file, $fileString);
    }

    protected  function writeOptionsAttributesInfile() {
        $optionsAttributes = $this->optionsAttributes;
        $file = $this->dbfolder.'/'.static::OPTIONS_ATTRIBUTES_INFILE_NAME.'.sql';
        if (file_exists($file)) {
            unlink($file);
        }
        $fileString = '';

        $optionsAttributeGroup = AttributeGroup::model()->findByAttributes(['type' => static::OPTIONS_ATTRIBUTE_GROUP_TYPE ]);
        $existedKeys = [];
        if ($this->isFake) {
            foreach ($this->fakeProperties as $key => $item) {
                if ($key == static::OPTIONS_ATTRIBUTE_GROUP_NAME) {
                    foreach ($item as $categoryKey => $attributes) {
                        foreach ($attributes as $attributeKey => $attributeItem) {
                            if (!in_array($attributeKey, $existedKeys)) {
                                $existedKeys[] = $attributeKey;
                                $fileString .= $attributeKey."\t";
                                $fileString .= $attributeItem."\t";
                                $fileString .= $optionsAttributeGroup->id."\t";
                                $fileString .= "\n";
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($optionsAttributes as $item) {
                $fileString .= $item['id']."\t";
                $fileString .= $item['value']."\t";
                $fileString .= $optionsAttributeGroup->id."\t";
                $fileString .= "\n";
            }
        }

        file_put_contents($file, $fileString);
    }

    protected function writeNormalizedProductsInfile() {

        $sql = "SELECT * FROM category;";
        $connection = Yii::app()->db;
        $command=$connection->createCommand($sql);
        $rows=$command->queryAll();
        $categories = [];
        foreach ($rows as $item) {
            $categories[$item['ext_id']] = $item['id'];
        }
        $normalizedProductsList = $this->normalizedProductsList;
        $file = $this->dbfolder.'/'.static::PRODUCTS_INFILE_NAME.'.sql';
        if (file_exists($file)) {
            unlink($file);
        }
        $fileString = '';
        foreach ($normalizedProductsList as $key => $item) {
            $fileString .= $item['id']."\t";
            $fileString .= $item['name']."\t";
            if (!empty($item['description'])) {
                $fileString .= str_replace("\t", "    ", Yii::app()->db->quoteValue($item['description']));
            } else {
                $fileString .= '\N';
            }
            $fileString .= "\t";
            $fileString .= $categories[$item['parent_id']]."\t";
            if (empty($item['properties'][static::WEIGHT_PROPERTY_NAME])) {
                $fileString .= '\N';
            } else {
                $fileString .= str_replace(',', '.', $item['properties'][static::WEIGHT_PROPERTY_NAME]);
            }
            $fileString .= "\t";
            $fileString .= mt_rand(1, 999)."\t";
            $fileString .= mt_rand(1, 999)."\t";
            $fileString .= mt_rand(1, 999);
            $fileString .= "\n";
        }
        file_put_contents($file, $fileString);
    }

    protected function writeProductsVariantsInfile() {

        $sql = "SELECT * FROM colour;";
        $connection = Yii::app()->db;
        $command=$connection->createCommand($sql);
        $rows=$command->queryAll();
        $colours = [];
        foreach ($rows as $item) {
            $colours[$item['ext_id']] = $item['id'];
        }

        $sql = "SELECT * FROM product;";
        $connection = Yii::app()->db;
        $command=$connection->createCommand($sql);
        $rows=$command->queryAll();
        $products = [];
        foreach ($rows as $item) {
            $products[$item['ext_id']] = $item['id'];
        }

        $normalizedProducts = $this->normalizedProducts;
        $file = $this->dbfolder.'/'.static::NORMALIZED_INFILE_NAME.'.sql';
        if (file_exists($file)) {
            unlink($file);
        }
        $fileString = '';
        foreach ($normalizedProducts as $key => $item) {
            $isFirst = true;
            $parentId = 0;
            foreach ($item as $productVariantKey => $variantItem) {
                if ($isFirst) {
                    $parentId = $variantItem['id'];
                }
                $isFirst = false;
                $fileString .= $variantItem['id']."\t";
                if (!empty($variantItem['articul'])) {
                    $fileString .= Yii::app()->db->quoteValue($variantItem['articul']);
                } else {
                    $fileString .= '\N';
                }
                $fileString .= "\t";
                if (empty($variantItem['properties'][static::COLOUR_PROPERTY_NAME])) {
                    $fileString .= '\N';
                } else {
                    $fileString .= $colours[$variantItem['properties'][static::COLOUR_PROPERTY_NAME]];
                }
                $fileString .= "\t";
                $fileString .= $products[$parentId]."\t";
                $fileString .= "\n";
            }

        }
        file_put_contents($file, $fileString);
    }

    protected function writeCatalogAttributesToProductInfile() {
        $normalizedProductsList = $this->normalizedProductsList;
        $file = $this->dbfolder.'/'.static::CATALOG_ATTRIBUTES_TO_PRODUCTS_INFILE_NAME.'.sql';
        if (file_exists($file)) {
            unlink($file);
        }
        $fileString = '';

        foreach ($normalizedProductsList as $item) {
            if (!empty($item['attributes'])) {
                foreach ($item['attributes'] as $key => $attribute) {
                    $fileString .= $item['id']."\t";
                    $fileString .= $attribute['id']."\t";
                    if (empty($attribute['value'])) {
                        $fileString .= '\N'."\n";
                    } else {
                        $fileString .= $attribute['value']."\n";
                    }
                }
            }
        }
        file_put_contents($file, $fileString);
    }

    protected function writePricesInfile() {
        $offers = $this->offers;
        $file = $this->dbfolder.'/'.static::OFFERS_INFILE_NAME.'.sql';
        if (file_exists($file)) {
            unlink($file);
        }
        $fileString = '';
        foreach ($offers as $item) {
            $fileString .= $item['id']."\t";
            $fileString .= $item['price']."\t";
            $fileString .= "\n";
        }
        file_put_contents($file, $fileString);
    }

    protected function writeProductsToStoragesInfile() {
        $offers = $this->offers;
        $file = $this->dbfolder.'/'.static::PRODUCTS_TO_STORAGES_INFILE_NAME.'.sql';
        if (file_exists($file)) {
            unlink($file);
        }
        $fileString = '';
        foreach ($offers as $item) {
            foreach ($item['storages'] as $storage) {
                $fileString .= $item['id']."\t";
                $fileString .= $storage['id']."\t";
                $fileString .= $storage['quantity']."\t";
                $fileString .= "\n";
            }
        }
        file_put_contents($file, $fileString);
    }

    public function actionImportOffers($xml, $dbfolder) {
        $this->dbfolder = $dbfolder;

        $this->startXML($xml, 'UTF-8');

        echo "Start parsing!\n";
        while( $this->parser->read() ) {
            $this->handleEvent();
        }
        $this->closeXML();
        echo "End parsing!\n";

        echo "Start writing to db_infile!\n";

        echo "Start writing storages!\n";
        $this->writeStoragesInfile();
        echo "End writing storages!\n";

        echo "Start writing offers!\n";
        $this->writePricesInfile();
        echo "End writing offers!\n";

        echo "Start writing products to prices!\n";
        $this->writeProductsToStoragesInfile();
        echo "End writing products to prices!\n";

        echo "End writing to db_infile!\n";

        echo "Start creating temporary tables!\n";

        echo "Start creating storage temporary table!\n";
        $this->createStorageTemporaryTable();
        echo "End creating storage temporary table!\n";

        echo "Start creating product variant to storage temporary table!\n";
        $this->createProductVariantToStorageTemporaryTable();
        echo "End creating product variant to storage temporary table!\n";

        echo "Start creating product variant price temporary table!\n";
        $this->createProductVariantPriceTemporaryTable();
        echo "End creating product variant price temporary table!\n";

        echo "End creating temporary tables!\n";

        echo "Start save db_infile to database!\n";

        echo "Start save db_infile to temporary_product_variant_price!\n";
        $this->saveProductVariantPriceInfileToTemporaryTable();
        $this->saveNewProductVariantPrice();
        echo "End save db_infile to temporary_product_variant_price!\n";

        echo "Start save db_infile to temporary_storage!\n";
        $this->saveStoragesInfileToTemporaryTable();
        $this->saveNewStorages();
        echo "End save db_infile to temporary_storage!\n";

        echo "Start save db_infile to temporary_product_variant_to_storage!\n";
        $this->saveProductsVariantsToStoragesToTemporaryTable();
        $this->saveNewProductsVariantsToStorages();
        echo "End save db_infile to temporary_product_variant_to_storage!\n";

        echo "End save db_infile to database!\n";

    }

    protected function onOffers() {
        while( $this->isHandleEvent() ) {
            if (
                $this->parser->nodeType == XMLReader::ELEMENT
            ) {
                if ( $this->parser->name == static::OFFER_TAG_NAME ) {
                    $this->currentOffer = [];
                }

                if ( $this->parser->name == static::ID_TAG_NAME ) {

                    if (empty($this->currentOffer['id'])) {
                        $this->currentOffer['id'] = $this->parser->readInnerXml();
                    }

                }

                if ( $this->parser->name == static::PRICES_TAG_NAME) {
                    $eventItem = [];
                    $eventItem['isEventStopped'] = $this->isEventStopped;
                    $eventItem['level'] = $this->level;
                    $eventItem['currentEventKey'] = $this->currentEventKey;
                    $this->handleEvent();
                    $this->isEventStopped = $eventItem['isEventStopped'];
                    $this->level = $eventItem['level'];
                    $this->currentEventKey = $eventItem['currentEventKey'];
                }

                if ( $this->parser->name == static::QUANTITY_TAG_NAME ) {

                    if (empty($this->currentOffer['quantity'])) {
                        $this->currentOffer['quantity'] = $this->parser->readInnerXml();
                    }

                }

                if ( $this->parser->name == static::STORAGE_TAG_NAME ) {
                    $storage['id'] = $this->parser->getAttribute(static::STORAGE_ID);
                    $storage['quantity'] = $this->parser->getAttribute(static::STORAGE_QUANTITY);
                    $this->currentOffer['storages'][] = $storage;
                }

            }

            if (
                $this->parser->nodeType == XMLReader::END_ELEMENT
            ) {
                if ( $this->parser->name == static::OFFER_TAG_NAME ) {
                    $this->offers[] = $this->currentOffer;
                }
            }
        }
    }

    protected function onPrices() {
        while( $this->isHandleEvent() ) {
            if (
                $this->parser->nodeType == XMLReader::ELEMENT
            ) {
                if ( $this->parser->name == static::PRICE_UNIT_TAG_NAME ) {
                    if (empty($this->currentOffer['price'])) {
                        $this->currentOffer['price'] = $this->parser->readInnerXml();
                    }
                }
            }
        }
    }

    protected function onStorages() {
        while( $this->isHandleEvent() ) {
            if (
                $this->parser->nodeType == XMLReader::ELEMENT
            ) {
                if ( $this->parser->name == static::STORAGE_TAG_NAME ) {
                    $this->currentStorage = [];
                }

                if ( $this->parser->name == static::ID_TAG_NAME ) {

                    if (empty($this->currentStorage['id'])) {
                        $this->currentStorage['id'] = $this->parser->readInnerXml();
                    }

                }

                if ( $this->parser->name == static::NAME_TAG_NAME ) {
                    if (empty($this->currentStorage['name'])) {
                        $this->currentStorage['name'] = $this->parser->readInnerXml();
                    }
                }
            }

            if (
                $this->parser->nodeType == XMLReader::END_ELEMENT
            ) {
                if ( $this->parser->name == static::STORAGE_TAG_NAME ) {
                    $this->storages[] = $this->currentStorage;
                }
            }
        }
    }


}