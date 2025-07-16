#!/usr/bin/env python3
# AI Benchmark module for Linux Benchmark
# Tests NumPy operations and scikit-learn machine learning performance

import time
import sys
import numpy as np
from sklearn.ensemble import RandomForestClassifier
from sklearn.datasets import make_classification
from sklearn.model_selection import train_test_split

# Function to measure execution time
def time_function(func, *args, **kwargs):
    start = time.time()
    result = func(*args, **kwargs)
    end = time.time()
    return end - start, result

# Test 1: NumPy matrix operations
def test_numpy_operations():
    print("Running NumPy matrix operations test...")
    
    # Create large matrices
    size = 2000
    matrix_a = np.random.random((size, size))
    matrix_b = np.random.random((size, size))
    
    # Matrix multiplication
    print("  Matrix multiplication test...")
    time_mult, _ = time_function(np.matmul, matrix_a, matrix_b)
    print(f"  Matrix multiplication time: {time_mult:.4f}s")
    
    # Matrix inversion (smaller matrix for speed)
    print("  Matrix inversion test...")
    smaller_matrix = np.random.random((1000, 1000))
    time_inv, _ = time_function(np.linalg.inv, smaller_matrix)
    print(f"  Matrix inversion time: {time_inv:.4f}s")
    
    # SVD decomposition
    print("  SVD decomposition test...")
    time_svd, _ = time_function(np.linalg.svd, smaller_matrix)
    print(f"  SVD decomposition time: {time_svd:.4f}s")
    
    return time_mult, time_inv, time_svd

# Test 2: Machine Learning operations
def test_machine_learning():
    print("Running Machine Learning test...")
    
    # Generate synthetic dataset
    print("  Generating dataset...")
    X, y = make_classification(n_samples=50000, n_features=20, 
                              n_informative=10, random_state=42)
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
    
    # Train RandomForest model
    print("  Training RandomForest model...")
    model = RandomForestClassifier(n_estimators=100, random_state=42)
    time_train, _ = time_function(model.fit, X_train, y_train)
    print(f"  Model training time: {time_train:.4f}s")
    
    # Prediction
    print("  Running prediction test...")
    time_predict, _ = time_function(model.predict, X_test)
    print(f"  Prediction time: {time_predict:.4f}s")
    
    return time_train, time_predict

# Main function
def main():
    print("Starting AI Benchmark tests...")
    results = {}
    
    try:
        # Test NumPy operations
        try:
            time_mult, time_inv, time_svd = test_numpy_operations()
            numpy_score = 1.0 / (time_mult + time_inv + time_svd) * 1000  # Higher score = better performance
            results["numpy_score"] = numpy_score
            results["matrix_mult_time"] = time_mult
            results["matrix_inv_time"] = time_inv
            results["svd_time"] = time_svd
        except Exception as e:
            print(f"Error during NumPy test: {e}")
            numpy_score = 0
            results["numpy_score"] = 0
            results["matrix_mult_time"] = 0
            results["matrix_inv_time"] = 0
            results["svd_time"] = 0
        
        # Test Machine Learning
        try:
            time_train, time_predict = test_machine_learning()
            ml_score = 1.0 / (time_train + time_predict) * 100  # Higher score = better performance
            results["ml_score"] = ml_score
            results["train_time"] = time_train
            results["predict_time"] = time_predict
        except Exception as e:
            print(f"Error during Machine Learning test: {e}")
            ml_score = 0
            results["ml_score"] = 0
            results["train_time"] = 0
            results["predict_time"] = 0
        
        # Calculate final score
        if numpy_score > 0 and ml_score > 0:
            # Weighted average: NumPy (60%), ML (40%)
            final_score = numpy_score * 0.6 + ml_score * 0.4
        elif numpy_score > 0:
            final_score = numpy_score * 0.8  # Only NumPy
        elif ml_score > 0:
            final_score = ml_score * 0.8  # Only ML
        else:
            final_score = 0
        
        results["total_score"] = final_score
        
        # Print results
        print("\nBenchmark Results:")
        print(f"NumPy Score: {numpy_score:.2f} points")
        print(f"Machine Learning Score: {ml_score:.2f} points")
        print(f"Total AI Score: {final_score:.2f} points")
        
        # Return results as a formatted string for bash script
        return_string = f"{final_score:.2f},{numpy_score:.2f},{ml_score:.2f},{time_mult:.4f},{time_inv:.4f},{time_svd:.4f},{time_train:.4f},{time_predict:.4f}"
        print(return_string)
        return 0
        
    except Exception as e:
        print(f"Error in AI benchmark: {e}")
        print("0,0,0,0,0,0,0,0")  # Return zeros on error
        return 1

if __name__ == "__main__":
    sys.exit(main())
