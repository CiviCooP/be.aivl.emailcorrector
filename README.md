Extension moved to https://lab.civicrm.org/partners/civicoop/aivl/be.aivl.emailcorrector

# be.aivl.emailcorrector

AIVL specifieke extensie om emailadressen te corrigeren. Ontwikkeld op basis van de EmailAmender extensie van futurefirst.co.uk omdat die niet beschikbaar is in CiviCRM 4.6. Wordt na overgang naar 4.7 bij AIVL samengevoegd met de EmailAmender extensie.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v5.4+
* CiviCRM (4.6 en hoger)



## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl be.aivl.emailcorrector@https://github.com/FIXME/be.aivl.emailcorrector/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/be.aivl.emailcorrector.git
cv en emailcorrector
```

## Usage

Extensie maakt menu item aan in Administer>Communications voor instellingen. Daarnaast moet handmatig een scheduled job aangemaakt worden voor de API EmailCorrect Fix. (Geen managed job omdat die altijd weer terugkomen na een clearcache ook al verwijder je de scheduled job!).

