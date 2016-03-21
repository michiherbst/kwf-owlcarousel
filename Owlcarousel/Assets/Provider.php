<?php
class Owlcarousel_Assets_Provider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'owlcarousel') {
            $cssReplacements = array();
            $replacements = array(
                '(function($,window,document,undefined){' => "var $=jQuery=require('jQuery');",
                '(function($,Modernizr,window,document,undefined){' => "var $=jQuery=require('jQuery');",
                '})(window.Zepto||window.jQuery,window,document);' => '',
                '})(window.Zepto||window.jQuery,window.Modernizr,window,document);' => '',
            );
            if (Kwf_Config::getValue('application.uniquePrefix')) {
                $cssReplacements['.owl-'] = '.'.Kwf_Config::getValue('application.uniquePrefix').'-owl-';
                $replacements['owl-'] = Kwf_Config::getValue('application.uniquePrefix').'-owl-';
            }
            $files = array(
                'owl.carousel/src/js/owl.carousel.js',
                'owl.carousel/src/css/owl.carousel.css',
                'owl.carousel/src/js/owl.autorefresh.js',
                //'owl.carousel/src/js/owl.lazyload.js',
                //'owl.carousel/src/css/owl.lazyload.css',
                'owl.carousel/src/js/owl.autoheight.js',
                'owl.carousel/src/css/owl.autoheight.css',
                //'owl.carousel/src/js/owl.video.js',
                //'owl.carousel/src/css/owl.video.css',
                'owl.carousel/src/js/owl.animate.js',
                'owl.carousel/src/css/owl.animate.css',
                'owl.carousel/src/js/owl.autoplay.js',
                'owl.carousel/src/js/owl.navigation.js',
                'owl.carousel/src/js/owl.hash.js',
                'owl.carousel/src/js/owl.support.modernizr.js',
                //'owl.carousel/src/css/owl.theme.default.css',
                //'owl.carousel/src/css/owl.theme.green.css',
            );


            //Support Kwf 4.0 ($needsProviderList=false) and 4.1+ ($needsProviderList=true)
            $reflection = new ReflectionClass('Kwf_Assets_Dependency_Abstract');
            $params = $reflection->getConstructor()->getParameters();
            $needsProviderList = false;
            if ($params && $params[0]->getName() == 'providerList') {
                $needsProviderList = true;
            }

            $deps = array();
            foreach ($files as $file) {
                $dep = Kwf_Assets_Dependency_File::createDependency($file, $this->_providerList);
                if ($dep->getMimeType() == 'text/javascript') {
                    $dep->setIsCommonJsEntry(true);
                    if ($needsProviderList) {
                        $dep = new Kwf_Assets_Dependency_Decorator_StringReplace($this->_providerList, $dep, $replacements);
                    } else {
                        $dep = new Kwf_Assets_Dependency_Decorator_StringReplace($dep, $replacements);
                    }
                    $dep->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS, $this->_providerList->findDependency('jQuery'), 'jQuery');
                } else {
                    if ($needsProviderList) {
                        $dep = new Kwf_Assets_Dependency_Decorator_StringReplace($this->_providerList, $dep, $cssReplacements);
                    } else {
                        $dep = new Kwf_Assets_Dependency_Decorator_StringReplace($dep, $cssReplacements);
                    }
                }
                $deps[] = $dep;
            }

            $deps[] = $this->_providerList->findDependency('ModernizrCssAnimations');
            $deps[] = $this->_providerList->findDependency('ModernizrCssTransitions');
            $deps[] = $this->_providerList->findDependency('ModernizrCssTransforms');
            $deps[] = $this->_providerList->findDependency('ModernizrCssTransforms3d');
            $deps[] = $this->_providerList->findDependency('ModernizrPrefixed');

            if ($needsProviderList) {
                $ret = new Kwf_Assets_Dependency_Dependencies($this->_providerList, $deps, $dependencyName);
            } else {
                $ret = new Kwf_Assets_Dependency_Dependencies($deps, $dependencyName);
            }
            return $ret;
        }

        return null;
    }
}
