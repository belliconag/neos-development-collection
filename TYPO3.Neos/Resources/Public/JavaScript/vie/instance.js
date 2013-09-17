define(['Library/vie'], function(VIE) {
	var vieInstance = new VIE();

	if (!vieInstance.namespaces.get('typo3')) {
		vieInstance.namespaces.add('typo3', 'http://www.typo3.org/ns/2012/Flow/Packages/Neos/Content/');
	}
	if (!vieInstance.namespaces.get('xsd')) {
		vieInstance.namespaces.add('xsd', 'http://www.w3.org/2001/XMLSchema#');
	}

	vieInstance.use(new vieInstance.RdfaService());

	return vieInstance;
});