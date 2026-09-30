const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('job_challans', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    challan_no: {
      type: DataTypes.STRING(255),
      allowNull: false,
      unique: "challan_no"
    },
    jc_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    sub_contractors_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    processing_type: {
      type: DataTypes.ENUM('Manufacturing','In Progress'),
      allowNull: false,
      defaultValue: "Manufacturing"
    },
    status: {
      type: DataTypes.ENUM('Pending','Received','Cancelled'),
      allowNull: false,
      defaultValue: "Pending"
    },
    total_amount: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true,
      defaultValue: 0.00
    },
    gst_amount: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true,
      defaultValue: 0.00
    },
    final_amount: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true,
      defaultValue: 0.00
    },
    vehicle_no: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    gst_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    estimated_values: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true,
      defaultValue: 0.00
    },
    expected_days: {
      type: DataTypes.INTEGER,
      allowNull: true,
      defaultValue: 0
    },
    work_description: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    semi_finished_item_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    modified: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    added_by: {
      type: DataTypes.INTEGER,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'job_challans',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
      {
        name: "challan_no",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "challan_no" },
        ]
      },
    ]
  });
};
