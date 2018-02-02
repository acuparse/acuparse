# Acuparse
## AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.

# DNS Redirect Settings
You will need to redirect your DNS so that your Access/smartHub uploads your data to Acuparse and not MyAcuRite directly.

If you are running Acuparse and your Access/smartHub locally on the same network. The DNS redirect will cause issues with Acuparse. It will not be able to upload it's data and instead upload data to itself.
In this case, you should select the secondary URL's in the MyAcuRite config settings. Acuparse will then upload readings to MyAcuRite as expected.

Secondary DNS entries point to the Acuparse domain hosted on Cloudflare.