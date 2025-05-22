.upsell-wrapper {
  padding: 10px;
  font-family: satoshi, arial;
}

.button, .lotuzpay-button {
  font-family: satoshi, arial;
  padding: 15px 40px;
  border: none;
  background: #2cc449;
  border-radius: 4px;
  color: white;
  font-size: 16px;
  margin-bottom: 15px;
  width: 100%;
  transition: .05s ease-in-out;
  cursor: pointer;
  font-weight: 500;
  letter-spacing: .5px;
  text-align: center;
}

.lotuzpay-button-modal {
  font-family: satoshi, arial;
  padding: 15px 40px;
  border: none;
  background: #ffffff;
  border-radius: 8px;
  color: #050207;
  font-size: 16px;
  width: 100%;
  transition: .05s ease-in-out;
  cursor: pointer;
  font-weight: 500;
  letter-spacing: .5px;
  text-align: center;
}

.lotuzpay-button-modal[disabled] {
  background: #82828224;
  color: #5f5f5f;
}

.button[disabled], .lotuzpay-button[disabled] {
  background: #9ce6aa;
}

.lotuzpay-button-reject {
  font-family: satoshi, arial;
    padding: 15px 40px;
    border: none;
    display: block;
    background: #c42c45;
    border-radius: 4px;
    color: white;
    font-size: 16px;
    width: 100%;
    transition: .05s ease-in-out;
    cursor: pointer;
    margin-bottom: 15px;
    font-weight: 500;
    letter-spacing: .5px;
    text-align: center;
}

.button[disabled], .lotuzpay-button-reject[disabled] {
  background: #e69cb5;
}

.button:hover, .lotuzpay-button:hover,
.lotuzpay-button-modal:hover,
.button:hover, .lotuzpay-button-reject:hover {
  scale: 1.02;
  position: relative;
}

.success-payment {
  display: none;
  padding: 10px;
  letter-spacing: 1px;
  border-radius: 4px;
  background-color: #d2fad2;
  color: #2a7437;
  font-size: 12px;
  font-family: 'satoshi';
  margin-top: 10px;
}

.error-payment {
  display: none;
  padding: 10px;
  letter-spacing: 1px;
  border-radius: 4px;
  background-color: rgb(248, 197, 197);
  color: #742a2a;
  font-size: 12px;
  font-family: 'satoshi';
  margin-top: 10px;
}

.loader {
  width: 20px;
  padding: 3px;
  aspect-ratio: 1;
  border-radius: 50%;
  background: #ffffff;
  --_m:
    conic-gradient(#0000 10%, #000),
    linear-gradient(#000 0 0) content-box;
  -webkit-mask: var(--_m);
  mask: var(--_m);
  -webkit-mask-composite: source-out;
  mask-composite: subtract;
  animation: l3 1s infinite linear;
}

@keyframes l3 {
  to {
    transform: rotate(1turn)
  }
}

.lotuzpay-modal {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  z-index: 9999999;
  /* background: radial-gradient(#411d51, #1f0d38); */
}

.lotuzpay-modal-content {
  width: 100%;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.lotuzpay-modal-body {
  width: 100%;
  max-width: 380px;
  background-color: white;
  border-radius: 15px;
  background: #272727;
  border: 1px solid #7e708324;
}

.lotuzpay-line {
  height: 1px;
  background: #72727224;
  width: 100%;
}

.lotuzpay-modal-title {
  font-weight: bold;
  font-size: 16px;
  padding: 15px 0;
  text-align: center;
  color: #ffffff96;
}

.lotuzpay-flex {
  display: flex;
}

@media (max-width: 767px) {
  .lotuzpay-flex {
  display: block;
}

}

.lotuzpay-justify-between {
  justify-content: space-between;
}

.lotuzpay-justify-center {
  justify-content: center;
}

.lotuzpay-items-center {
  align-items: center;
}

.lotuzpay-justify-end {
  justify-content: end;
}

.lotuzpay-product-info-area {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.lotuzpay-gap {
  gap: 5px;
}

.lotuzpay-gap-2 {
  gap: 10px;
}

.lotuzpay-mt-1 {
  margin-top: 10px;
}

.lotuzpay-mt-2 {
  margin-top: 20px;
}

.lotuzpay-mx-1 {
  margin-left: 10px;
  margin-right: 10px;
}

.lotuzpay-mx-2 {
  margin-left: 20px;
  margin-right: 20px;
}

.lotuzpay-my-1 {
  margin-top: 10px;
  margin-bottom: 10px;
}

.lotuzpay-my-2 {
  margin-top: 20px;
  margin-bottom: 20px;
}

.lotuzpay-m-1 {
  margin: 10px;
}

.lotuzpay-green {
  color: #2cc449
}

.lotuzpay-card-area {
  background: #111111;
  border-radius: 4px;
  padding: 10px 20px;
  font-weight: bold;
  font-size: 12px;
  color: white;
  margin: 20px 10px;
}

.lotuzpay-small-text {
  font-size: 12px;
}

.lotuzpay-text-color {
  color: #999;
}

.lotuzpay-disclaimer {
  color: #999;
  line-height: 17px;
}

.lotuzpay-close-area {
  position: relative;
  width: 100%;
  height: 0;
  /* top: -26px;
  right: -24px; */
}

.lotuzpay-close {
  background: #6a6a6a17;
  color: #4f4f4f82;
  border-radius: 50%;
  /* padding: 10px; */
  cursor: pointer;
  margin-top: 8px;
  margin-right: 8px;
  width: 38px;
  height: 38px;
  text-align: center;
  display: flex;
  justify-content: center;
  align-items: center;
}

