# guidonciniverdi

Questo repo contiene il codice del tema e del plugin Wordpress per il sito guidonciniverdi.it aperto al grande pubblico.

Il tema utilizza la tecnologia "a blocchi" Gutenberg di Wordpress, più Tailwind CSS (si veda questo post che spiega i perché di un approccio ibrido: ["Gutenberg Full Site Editing does not have to be full"](https://extendify.com/gutenberg-full-site-editing-does-not-have-to-be-full/)).

## Get started

Usiamo [docker-compose](https://docs.docker.com/compose/gettingstarted/) per semplificare la gestione dell'ambiente di sviluppo.
Ecco i tre comandi chiave:

```
docker-compose pull  # Ottieni le immagini dal registry docker
docker-compose run frontend npm install  # Scarica i pacchetti Node.js che servono per la compilazione dello stile
docker-compose up  # Carica l'ambiente di sviluppo: Wordpress + base dati + frontend + hot reloader
```
