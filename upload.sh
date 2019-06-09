#!/bin/bash
git add . && git commit -m 'upload' && git pull upstream master &&  git push upstream master
