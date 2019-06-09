#!/bin/bash
git add . && git commit -m 'upload' && git pull upsteam master &&  git push upstream master
