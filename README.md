# AP3SiteFilms

## Ajouter mes modifs
Quand tu as fini ton travail, il faut l'ajouter à ton repository local avec un 
> ```git add .``` 

#### Conseil : le faire à chaque modif et avant de partir le soir

Ensuite, tu dois faire un commit, c'est-à-dire enregitrer les modifications et tout, et pour ça faire un 

> ```git commit -m "Nom du commit"``` .

Le nom du commit doit être explicit, il sert de commentiare aussi.
Après faire un :

> ```git push origin "nom de ta branche"``` .

Pour que ça l'ajoute sur la branche, et de là tout le monde pourra le pull sur la branche en distant.
#### Conseil : le faire vraiment quand tu as fini les modifs, fini le ticket, pas juste pour des petits trucs.


Une fois sur votre branche, le G vérifiera le code et acceptera ou non. Il mettra les modifs sur la branche de test avant d'approuver ou rejeter ton code.

## Récupérer des nouvelles modifications

Quand une modification vient d'être ajoutée au projet final, il faut la récupérer en local, sinon tu l'auras pas. 

Du coup, il faut faire un :

> ```git fetch``` .

Cela va lire les modifications et tout et donc mettre à jour tes branches en local.

> Pour voir sur quelle branche vous êtes, faire un ```git branch```.

Une fois tes branches à jour, il faut mettre les fichiers de code à jour, pour vraiment prendre en compte les nouveautés. Pour cela faire un :

> ```git rebase main``` .

Cela va donc "copier" le code de la branche main sur tous les fichiers modifiés, et donc mettre tout à jour. Tu auras toutes les dernières modifications.

#### Faire ces 2 commandes quand tu commences ta journée pour être sûr d'être à jour.

## Règles :
> + Ne pas être à 2 sur le même fichier 
> + Ne pas crée un fichier avec un nom qui existe déjà
> + Ne pas merge sur test pour rien, seulement les gros trucs
> + Ne créez pas de fichiers inutiles, consultez le G au préalable 
> + Ne pas suprimez de fichiers crées pas les autres
> + _**Ne pas travailler sur la branche main ou test**_