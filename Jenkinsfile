// #!groovy

@Library('addons') _

pipeline {
  agent any
  options {
    disableConcurrentBuilds()
  }
  tools {nodejs "node"}
  stages {
    stage('Lint') {
        steps {
            sh 'helm lint ./omni-TIMEZONE'
        }
    }
 

    stage('Deploy to Demo server') {
        when {
            anyOf {
                branch 'demo'
            }
        } 
        steps {
          echo "Declaring variables on Jenkins server"
          withCredentials([
            string(credentialsId: 'paypecker_aws_access_key', variable: 'paypecker_aws_access_key'),
            string(credentialsId: 'paypecker_aws_secret_key', variable: 'paypecker_aws_secret_key'),
            file(credentialsId: 'OMNI_TIMEZONE_DEMO_ENV', variable: 'OMNI_TIMEZONE_DEMO_ENV'),
            string(credentialsId: 'APM_SECRET_TOKEN', variable: 'APM_SECRET_TOKEN'),
            string(credentialsId: 'ELASTIC_CLOUD_ID', variable: 'ELASTIC_CLOUD_ID'),
            string(credentialsId: 'ELASTIC_CLOUD_AUTH', variable: 'ELASTIC_CLOUD_AUTH'),
          ]) {
              sh "rm -rf  .env | true"
              sh ("cp ${OMNI_TIMEZONE_DEMO_ENV} .env | true")
              sh "sed -i 's~SECRET_TOKEN~${APM_SECRET_TOKEN}~g' ./php/local.ini"
              sh "sed -i 's~My-service~timezone~g' ./php/local.ini"
              sh "sed -i 's~ENVIRONMENT~DEMO~g' ./php/local.ini"
              sh "sed -i 's~http://localhost:8200~https://paypecker-default.apm.eu-west-1.aws.found.io~g' ./php/local.ini"
              sh "aws configure set aws_access_key_id '${paypecker_aws_access_key}' --profile paypecker"
              sh "aws configure set aws_secret_access_key '${paypecker_aws_secret_key}' --profile paypecker"
              sh "aws configure set region 'eu-west-1' --profile paypecker"
              sh "aws configure set output 'json' --profile paypecker"
              sh 'aws eks --region eu-west-1 update-kubeconfig --name Omni --profile paypecker'
              sh 'docker build . -f ./deploy/Dockerfile --build-arg CLOUD_ID=${ELASTIC_CLOUD_ID} --build-arg CLOUD_AUTH=${ELASTIC_CLOUD_AUTH} --build-arg SERVICE_ENVIRONMENT=dev --build-arg SERVICE_NAME=${appName} -t ${appName}' 
              sh 'docker tag ${appName}:latest ${IMAGETAGDEMO}'
              sh 'aws ecr get-login --no-include-email --region eu-west-1 --profile paypecker > pass.sh'
              sh 'chmod +x pass.sh && chmod 777 pass.sh && ./pass.sh'
              sh 'docker push ${IMAGETAGDEMO}'
              sh 'curl -sSL https://raw.githubusercontent.com/helm/helm/master/scripts/get-helm-3 | bash'
              sh 'kubectl create namespace omni-demo | true'
              //sh 'helm uninstall ${appName} --namespace omni-demo |true'
              //sh "helm install --namespace omni-demo ${appName} ./omni-timezone/ --set=image.tag=${env.BUILD_NUMBER} -f ./omni-timezone/values-demo.yaml"
              sh "helm upgrade -f ./omni-timezone/values-demo.yaml --namespace omni-demo ${appName} ./omni-timezone/ --atomic --timeout 600s --set=image.tag=${env.BUILD_NUMBER}"   
          }
        }
    }



    stage('Deploy to Dev server') {
        when {
            anyOf {
                branch 'dev'
                branch 'log-to-elastic'
            }
        } 
        steps {
          echo "Declaring variables on Jenkins server"
          withCredentials([
            string(credentialsId: 'paypecker_aws_access_key', variable: 'paypecker_aws_access_key'),
            string(credentialsId: 'paypecker_aws_secret_key', variable: 'paypecker_aws_secret_key'),
            file(credentialsId: 'OMNI_TIMEZONE_DEV_ENV', variable: 'OMNI_TIMEZONE_DEV_ENV'),
            string(credentialsId: 'APM_SECRET_TOKEN', variable: 'APM_SECRET_TOKEN'),
            string(credentialsId: 'ELASTIC_CLOUD_ID', variable: 'ELASTIC_CLOUD_ID'),
            string(credentialsId: 'ELASTIC_CLOUD_AUTH', variable: 'ELASTIC_CLOUD_AUTH'),
          ]) {
              sh "rm -rf  .env | true"
              sh ("cp ${OMNI_TIMEZONE_DEV_ENV} .env | true")
              sh "sed -i 's~SECRET_TOKEN~${APM_SECRET_TOKEN}~g' ./php/local.ini"
              sh "sed -i 's~My-service~timezone~g' ./php/local.ini"
              sh "sed -i 's~ENVIRONMENT~DEVELOPMENT~g' ./php/local.ini"
              sh "sed -i 's~http://localhost:8200~https://paypecker-default.apm.eu-west-1.aws.found.io~g' ./php/local.ini"
              sh "aws configure set aws_access_key_id '${paypecker_aws_access_key}' --profile paypecker"
              sh "aws configure set aws_secret_access_key '${paypecker_aws_secret_key}' --profile paypecker"
              sh "aws configure set region 'eu-west-1' --profile paypecker"
              sh "aws configure set output 'json' --profile paypecker"
              sh 'aws eks --region eu-west-1 update-kubeconfig --name Omni --profile paypecker'
              sh 'docker build . -f ./deploy/Dockerfile --build-arg CLOUD_ID=${ELASTIC_CLOUD_ID} --build-arg CLOUD_AUTH=${ELASTIC_CLOUD_AUTH} --build-arg SERVICE_ENVIRONMENT=dev --build-arg SERVICE_NAME=${appName} -t ${appName}'
              sh 'docker tag ${appName}:latest ${IMAGETAGDEV}'
              sh 'aws ecr get-login --no-include-email --region eu-west-1 --profile paypecker > pass.sh'
              sh 'chmod +x pass.sh && chmod 777 pass.sh && ./pass.sh'
              sh 'docker push ${IMAGETAGDEV}'
              sh 'curl -sSL https://raw.githubusercontent.com/helm/helm/master/scripts/get-helm-3 | bash'
              sh 'kubectl create namespace omni-dev | true'
              //sh 'helm uninstall ${appName} --namespace omni-dev |true'
              //sh "helm install --namespace omni-dev ${appName} ./omni-timezone/ --set=image.tag=${env.BUILD_NUMBER} -f ./omni-timezone/values-dev.yaml"
              sh "helm upgrade -f ./omni-timezone/values-dev.yaml --namespace omni-dev ${appName} ./omni-timezone/ --atomic --timeout 600s --set=image.tag=${env.BUILD_NUMBER}"   
          }
        }
    }



    stage('Deploy to Prod server') {
        when {
            anyOf {
                branch 'main'
                branch 'master'
            }
        } 
        steps {
          echo "Declaring variables on Jenkins server"
          withCredentials([
            string(credentialsId: 'paypecker_aws_access_key', variable: 'paypecker_aws_access_key'),
            string(credentialsId: 'paypecker_aws_secret_key', variable: 'paypecker_aws_secret_key'),
            file(credentialsId: 'OMNI_TIMEZONE_PROD_ENV', variable: 'OMNI_TIMEZONE_PROD_ENV'),
          ]) {
              sh "rm -rf  .env | true"
              sh ("cp ${OMNI_TIMEZONE_PROD_ENV} .env | true")
              sh "aws configure set aws_access_key_id '${paypecker_aws_access_key}' --profile paypecker"
              sh "aws configure set aws_secret_access_key '${paypecker_aws_secret_key}' --profile paypecker"
              sh "aws configure set region 'eu-west-1' --profile paypecker"
              sh "aws configure set output 'json' --profile paypecker"
              sh 'aws eks --region eu-west-1 update-kubeconfig --name Omni --profile paypecker'
              sh 'docker build . -f ./deploy/Dockerfile -t ${appName}' 
              sh 'docker tag ${appName}:latest ${IMAGETAGPROD}'
              sh 'aws ecr get-login --no-include-email --region eu-west-1 --profile paypecker > pass.sh'
              sh 'chmod +x pass.sh && chmod 777 pass.sh && ./pass.sh'
              sh 'docker push ${IMAGETAGPROD}'
              sh 'curl -sSL https://raw.githubusercontent.com/helm/helm/master/scripts/get-helm-3 | bash'
              sh 'kubectl create namespace omni-prod | true'
              //sh 'helm uninstall ${appName} --namespace omni-prod |true'
              sh "helm install --namespace omni-prod ${appName} ./omni-timezone/ --set=image.tag=${env.BUILD_NUMBER} -f ./omni-timezone/values-prod.yaml"
              //sh "helm upgrade -f ./omni-timezone/values-prod.yaml --namespace omni-prod ${appName} ./omni-timezone/ --atomic --timeout 600s --set=image.tag=${env.BUILD_NUMBER}"   
          }
        }
    }
  }




  post {
    always {
      deleteDir()
    }
    failure {
      slackSend (color: '#FF0000', message: "The pipeline build at <${env.BUILD_URL}|${env.BUILD_TAG}> was unstable and failed. Please click <${env.BUILD_URL}console|here> to view the error log and carry out necessary fix(es). Cc: @channel")
    }
    success {
      slackSend (color: '#7FFF00', message: "The pipeline build at <${env.BUILD_URL}|${env.BUILD_TAG}> was succesfull. Cc: @channel")
    }
  }
  environment {
    AWS_DEFAULT_REGION = 'eu-west-1'
    appName = 'omni-timezone'
    ECR = '113717999148.dkr.ecr.eu-west-1.amazonaws.com'
    IMAGETAGSTAGING = "${ECR}/${appName}-stage:${env.BUILD_NUMBER}"
    IMAGETAGDEV = "${ECR}/${appName}-dev:${env.BUILD_NUMBER}"
    IMAGETAGDEMO = "${ECR}/${appName}-demo:${env.BUILD_NUMBER}"
    IMAGETAGPROD = "${ECR}/${appName}-prod:${env.BUILD_NUMBER}"
    IMAGETAGQAT = "${ECR}/${appName}-qat:${env.BUILD_NUMBER}"
    IMAGETAGMASTER = "${ECR}/${appName}:${env.BUILD_NUMBER}"
    IMAGETAGSANDBOX = "${ECR}/${appName}-sandbox:${env.BUILD_NUMBER}"
  }
}