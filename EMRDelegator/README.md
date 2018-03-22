[![coverage report](https://gitlab.webpt.com/webpt/emrdelegator/badges/develop/coverage.svg)](https://gitlab.webpt.com/webpt/emrdelegator/commits/develop)

EMRDelegator
============

Application to route authenticated Members and Super Users into the appropriate cluster, and to preset Members with Agreements such as Business Associates Agreement (BAA), Terms of Service (TOS), etc.

Authenticates via [EMRAuth](https://gitlab.webpt.com/webpt/emrauth).

This application is also the System of Record for Cluster, Company, and Facility entities, as well as the relevant Org and User associations.

## Provisioning
[https://gitlab.webpt.com/webpt/provisioning/tree/develop/Vagrant/provisions/php56](https://gitlab.webpt.com/webpt/provisioning/tree/develop/Vagrant/provisions/php56)

## API Documentation
Explore [http://auth.webpt56.vagrant/api/docs/service.v1.swagger.json](api/docs/service.v1.swagger.json) using [Swagger UI](http://auth.webpt56.vagrant/swagger-ui) after Provisioning EMR.
