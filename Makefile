CONDA_ENV  := site_web
ifdef NOCONDA
CONDA_RUN  :=
else
CONDA_RUN  := conda run -n $(CONDA_ENV) --no-capture-output
endif

WEBROOT    := html
PORT       := 8080
PID_FILE   := .php-server.pid

R  := \033[0m
B  := \033[1m
G  := \033[32m
Y  := \033[33m
C  := \033[36m

PHP_FILES  := $(shell find $(WEBROOT) -name "*.php")

.DEFAULT_GOAL := help
.PHONY: help venv venv-update livetest stop test deploy clean update-icons

help: ## Cette aide
	@printf "$(B)$(C)site_web — Tâches de développement$(R)\n\n"
	@printf "$(Y)Usage :$(R) make $(G)<cible>$(R)\n\n"
	@printf "$(Y)Cibles :$(R)\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS=":.*?## "}; {printf "  $(G)%-12s$(R) %s\n", $$1, $$2}'
	@printf "\n$(Y)Variables :$(R)\n"
	@printf "  $(G)PORT$(R)         Port du serveur PHP local (défaut : $(Y)$(PORT)$(R))\n"
	@printf "  $(G)NOCONDA$(R)      Contourne conda ; les outils doivent être dans le PATH\n"
	@printf "                 ex. $(C)make test NOCONDA=1$(R)\n"
	@printf "                 Requis pour livetest avec MariaDB : le PHP conda n'a pas\n"
	@printf "                 pdo_mysql ni mbstring. Installer d'abord :\n"
	@printf "                 $(C)sudo apt install php-mysql php-mbstring$(R)\n"

venv: ## Crée l'environnement conda '$(CONDA_ENV)' depuis environment.yml
	@printf "$(C)Création de l'environnement conda '$(CONDA_ENV)'…$(R)\n"
	conda env create -f environment.yml
	@printf "$(G)Fait ! Activer avec :$(R) conda activate $(CONDA_ENV)\n"

venv-update: ## Met à jour l'environnement conda depuis environment.yml
	@printf "$(C)Mise à jour de l'environnement conda '$(CONDA_ENV)'…$(R)\n"
	conda env update -f environment.yml --prune
	@printf "$(G)Fait.$(R)\n"

livetest: ## Démarre le serveur PHP local et ouvre le navigateur (détaché)
	@printf "$(C)Démarrage du serveur PHP sur http://localhost:$(PORT) …$(R)\n"
	@$(CONDA_RUN) php -S localhost:$(PORT) -t $(WEBROOT) \
	    > /tmp/php-site_web.log 2>&1 & \
	echo $$! > $(PID_FILE)
	@sleep 1
	@printf "$(G)Serveur démarré (PID $$(cat $(PID_FILE))).$(R)\n"
	@printf "$(G)Logs :$(R) $(Y)/tmp/php-site_web.log$(R)\n"
	@xdg-open http://localhost:$(PORT) 2>/dev/null &

stop: ## Arrête le serveur PHP local
	@killed=0; \
	if [ -f $(PID_FILE) ]; then \
	    pid=$$(cat $(PID_FILE)); \
	    kill $$pid 2>/dev/null && killed=1; \
	    rm -f $(PID_FILE); \
	fi; \
	pkill -f "php -S localhost:$(PORT)" 2>/dev/null && killed=1; \
	if [ $$killed -eq 1 ]; then \
	    printf "$(G)Serveur arrêté.$(R)\n"; \
	else \
	    printf "$(Y)Aucun serveur en cours.$(R)\n"; \
	fi

test: ## Vérifie la syntaxe PHP de tous les fichiers du site
	@printf "$(C)Vérification syntaxique PHP…$(R)\n"
	@errors=0; \
	for f in $(PHP_FILES); do \
	    result=$$($(CONDA_RUN) php -l "$$f" 2>&1); \
	    if echo "$$result" | grep -q "^No syntax errors"; then \
	        printf "  $(G)OK$(R)  $$f\n"; \
	    else \
	        printf "  $(Y)ERR$(R) $$f\n"; \
	        echo "$$result" | grep -v "^No syntax errors"; \
	        errors=$$((errors + 1)); \
	    fi; \
	done; \
	if [ $$errors -eq 0 ]; then \
	    printf "$(G)Tous les fichiers sont valides.$(R)\n"; \
	else \
	    printf "$(Y)$$errors fichier(s) en erreur.$(R)\n"; \
	    exit 1; \
	fi

update-icons: ## Met à jour html/images/*.png depuis le dépôt PBIcons
	@tools/update_icons.sh

deploy: ## Déploie le site sur le serveur (sitecopy)
	sitecopy -u cardolan

clean: ## Supprime les fichiers temporaires (PID, logs)
	@rm -f $(PID_FILE) /tmp/php-site_web.log
	@printf "$(G)Nettoyé.$(R)\n"
